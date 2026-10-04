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
