<?php

namespace Tests\ChessBundle\Domain\Model\Knight;

use Chess\Domain\Model\Board\Board;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Board\InvalidBoxException;
use Chess\Domain\Model\Board\NotFoundBoardException;
use Chess\Domain\Model\Knight\GetMovesFromSourceToDestination;
use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Domain\Model\Knight\KnightRepository;
use Chess\Domain\Model\Knight\NotFoundKnightException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests of Knight's domain model service GetMovesFromSourceToDestination.
 *
 * @package Tests\ChessBundle\Domain\Model\Knight
 */
class GetMovesFromSourceToDestinationTest extends TestCase
{
    /** @var KnightRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $knightRepository;

    /** @var BoardRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $boardRepository;

    /** @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $eventDispatcher;

    /** @var GetMovesFromSourceToDestination */
    private $getKnightMovesService;

    /**
     * Set up tests.
     */
    protected function setUp()
    {
        $this->knightRepository = $this->createMock(KnightRepository::class);
        $this->boardRepository = $this->createMock(BoardRepository::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->getKnightMovesService = new GetMovesFromSourceToDestination(
            $this->knightRepository,
            $this->boardRepository,
            $this->eventDispatcher
        );
    }

    /**
     * Knight's minimum moves from source to destination data provider.
     *
     * @return array
     */
    public function knightsMinimumMovesFromSourceToDestinationDataProvider()
    {
        $knightId = new KnightId('71111111-1111-1111-1111-111111111117');
        $boardId = new BoardId('91111111-1111-1111-1111-111111111119');

        return [
            [ $boardId, $knightId, new Box(0), new Box(10), 1 ],
            [ $boardId, $knightId, new Box(0), new Box(17), 1 ],
            [ $boardId, $knightId, new Box(0), new Box(2), 2 ],
            [ $boardId, $knightId, new Box(0), new Box(16), 2 ],
            [ $boardId, $knightId, new Box(0), new Box(1), 3 ],
            [ $boardId, $knightId, new Box(0), new Box(63), 6 ],
        ];
    }

    /**
     * Tests Knight's minimum moves service from source to destination.
     *
     * @dataProvider knightsMinimumMovesFromSourceToDestinationDataProvider
     *
     * @param BoardId   $boardId       Board identifier.
     * @param KnightId  $knightId      Knight identifier.
     * @param Box       $source        Source box.
     * @param Box       $destination   Destination box.
     * @param int       $expectedMoves Expected moves.
     *
     * @throws InvalidBoxException
     * @throws NotFoundBoardException
     * @throws NotFoundKnightException
     */
    public function testKnightMinimumMovesFromSourceToDestination(BoardId $boardId, KnightId $knightId, Box $source, Box $destination, int $expectedMoves)
    {
        $knight = new Knight($knightId);
        $board = new Board($boardId);

        $this->boardRepository
            ->expects(static::any())
            ->method('ofIdOrFail')
            ->with(static::equalTo($boardId))
            ->willReturn($board);

        $this->knightRepository
            ->expects(static::any())
            ->method('ofIdOrFail')
            ->with(static::equalTo($knightId))
            ->willReturn($knight);

        $this->getKnightMovesService->execute($boardId, $knightId, $source, $destination, []);

        static::assertCount($expectedMoves, $this->getKnightMovesService->getMoves());
    }
}