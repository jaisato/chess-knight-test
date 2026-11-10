<?php

declare(strict_types=1);

namespace Chess\Tests\Domain\Model\Knight;

use Chess\Domain\Event\Knight\NewShortestPathFound;
use Chess\Domain\Model\Board\Board;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\GetMovesFromSourceToDestination;
use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Domain\Model\Knight\KnightRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Tests of Knight's domain model service GetMovesFromSourceToDestination.
 */
final class GetMovesFromSourceToDestinationTest extends TestCase
{
    /**
     * Knight's minimum moves from source to destination data provider.
     *
     * @return iterable<string, array{Box, Box, int}>
     */
    public static function knightsMinimumMovesFromSourceToDestinationDataProvider(): iterable
    {
        yield 'a1 to c2' => [new Box(0), new Box(10), 1];
        yield 'a1 to b3' => [new Box(0), new Box(17), 1];
        yield 'a1 to c1' => [new Box(0), new Box(2), 2];
        yield 'a1 to a3' => [new Box(0), new Box(16), 2];
        yield 'a1 to b1' => [new Box(0), new Box(1), 3];
        yield 'a1 to h8' => [new Box(0), new Box(63), 6];
        // The diagonal neighbour of a corner is the one square a knight needs
        // four moves for, where the middle of the board needs two.
        yield 'a1 to b2, the corner exception' => [new Box(0), new Box(9), 4];
        yield 'h8 to g7, the same from the other corner' => [new Box(63), new Box(54), 4];
        yield 'd4 to e5, a diagonal neighbour in the middle' => [new Box(27), new Box(36), 2];
        yield 'h1 to a8' => [new Box(7), new Box(56), 6];
    }

    /**
     * Tests Knight's minimum moves service from source to destination.
     */
    #[DataProvider('knightsMinimumMovesFromSourceToDestinationDataProvider')]
    public function testKnightMinimumMovesFromSourceToDestination(Box $source, Box $destination, int $expectedMoves): void
    {
        $service = $this->createService($source, $destination);

        static::assertTrue($service->execute(self::boardId(), self::knightId(), $source, $destination, []));
        static::assertCount($expectedMoves, $service->getMoves() ?? []);
    }

    /**
     * The returned path has to be a path: every step a legal knight move, the
     * first one reachable from the source, the last one the destination.
     *
     * Counting the moves says the answer is the right length but not that it
     * describes a route the knight could take, so a search that returned the
     * right number of arbitrary squares would have passed.
     */
    #[DataProvider('knightsMinimumMovesFromSourceToDestinationDataProvider')]
    public function testTheReturnedSolutionIsAWalkableKnightPath(Box $source, Box $destination, int $expectedMoves): void
    {
        $service = $this->createService($source, $destination);

        $service->execute(self::boardId(), self::knightId(), $source, $destination, []);

        $path = $service->getMoves();

        static::assertNotNull($path, 'A reachable destination must produce a path.');
        static::assertCount($expectedMoves, $path);
        static::assertTrue(
            $path[\count($path) - 1]->equalsTo($destination),
            'The path must end on the destination box.',
        );

        $previous = $source;
        foreach ($path as $step) {
            $dx = abs($step->getX() - $previous->getX());
            $dy = abs($step->getY() - $previous->getY());

            static::assertTrue(
                (1 === $dx && 2 === $dy) || (2 === $dx && 1 === $dy),
                \sprintf(
                    'Step from (%d,%d) to (%d,%d) is not a knight move.',
                    $previous->getX(),
                    $previous->getY(),
                    $step->getX(),
                    $step->getY(),
                ),
            );

            $previous = $step;
        }
    }

    /**
     * A knight already on its destination needs no move, and the search says
     * so - with an empty path, not null, and with the event still announced.
     */
    public function testTheSameSquareIsReachedInNoMoves(): void
    {
        $square = new Box(27);
        $service = $this->createService($square, $square);

        static::assertTrue($service->execute(self::boardId(), self::knightId(), $square, $square, []));
        static::assertSame([], $service->getMoves());
    }

    /**
     * Every ordered pair of squares, against a breadth-first search written
     * here on plain integers rather than on Board, Box and Move.
     *
     * The cases above are a handful of pairs, most of them from a1. The search
     * has no reason to treat one square differently from another, but a fault
     * in how the board bounds or applies a move would show on some squares and
     * not others - and the edges and corners, where a knight has two to six
     * moves instead of eight, are exactly where it would. 4096 searches take
     * well under a second.
     */
    public function testEveryPairOfSquaresGetsAShortestWalkablePath(): void
    {
        $boards = $this->createStub(BoardRepository::class);
        $boards->method('ofIdOrFail')->willReturn(new Board(self::boardId()));
        $knights = $this->createStub(KnightRepository::class);
        $knights->method('ofIdOrFail')->willReturn(new Knight(self::knightId()));
        $dispatcher = $this->createStub(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturnArgument(0);

        $service = new GetMovesFromSourceToDestination($knights, $boards, $dispatcher);
        $squares = Board::NUMBER_OF_COLUMNS * Board::NUMBER_OF_ROWS;

        for ($from = 0; $from < $squares; ++$from) {
            $expected = self::referenceDistancesFrom($from);

            for ($to = 0; $to < $squares; ++$to) {
                static::assertTrue($service->execute(self::boardId(), self::knightId(), new Box($from), new Box($to), []));

                $path = $service->getMoves() ?? [];
                $route = \sprintf('%d to %d', $from, $to);

                static::assertCount($expected[$to], $path, $route . ' is not a shortest path');

                $previous = $from;
                foreach ($path as $step) {
                    $square = $step->getOneDimensionValue();
                    static::assertTrue(self::isKnightMove($previous, $square), \sprintf('%s steps from %d to %d', $route, $previous, $square));
                    $previous = $square;
                }

                static::assertSame($to, $previous, $route . ' does not end on the destination');
            }
        }
    }

    /**
     * @return array<int, int> moves from $from to every square, by square
     */
    private static function referenceDistancesFrom(int $from): array
    {
        $distance = [$from => 0];
        $queue = [$from];

        for ($head = 0; $head < \count($queue); ++$head) {
            $square = $queue[$head];

            for ($next = 0; $next < Board::NUMBER_OF_COLUMNS * Board::NUMBER_OF_ROWS; ++$next) {
                if (!isset($distance[$next]) && self::isKnightMove($square, $next)) {
                    $distance[$next] = $distance[$square] + 1;
                    $queue[] = $next;
                }
            }
        }

        return $distance;
    }

    private static function isKnightMove(int $from, int $to): bool
    {
        $dx = abs($from % Board::NUMBER_OF_COLUMNS - $to % Board::NUMBER_OF_COLUMNS);
        $dy = abs(intdiv($from, Board::NUMBER_OF_COLUMNS) - intdiv($to, Board::NUMBER_OF_COLUMNS));

        return (1 === $dx && 2 === $dy) || (2 === $dx && 1 === $dy);
    }

    /**
     * The service with repositories that hold the board and the knight, and a
     * dispatcher that expects exactly one event announcing the searched path.
     */
    private function createService(Box $source, Box $destination): GetMovesFromSourceToDestination
    {
        $boardRepository = $this->createMock(BoardRepository::class);
        $boardRepository
            ->expects(static::once())
            ->method('ofIdOrFail')
            ->with(static::equalTo(self::boardId()))
            ->willReturn(new Board(self::boardId()));

        $knightRepository = $this->createMock(KnightRepository::class);
        $knightRepository
            ->expects(static::once())
            ->method('ofIdOrFail')
            ->with(static::equalTo(self::knightId()))
            ->willReturn(new Knight(self::knightId()));

        // The event carries the endpoints of the path it announces: the real
        // source, not square 0 (the old search reported 0 for every request).
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(static::once())
            ->method('dispatch')
            ->with(static::callback(
                static fn(NewShortestPathFound $event): bool => $event->origin()->equalsTo($source)
                    && $event->destination()->equalsTo($destination),
            ))
            ->willReturnArgument(0);

        return new GetMovesFromSourceToDestination($knightRepository, $boardRepository, $eventDispatcher);
    }

    private static function boardId(): BoardId
    {
        return new BoardId('91111111-1111-1111-1111-111111111119');
    }

    private static function knightId(): KnightId
    {
        return new KnightId('71111111-1111-1111-1111-111111111117');
    }
}
