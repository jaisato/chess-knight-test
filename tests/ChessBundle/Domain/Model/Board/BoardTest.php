<?php

namespace Tests\ChessBundle\Domain\Model\Board;

use Chess\Domain\Model\Board\Board;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Domain\Model\Knight\Move;
use PHPUnit\Framework\TestCase;

/**
 * Tests of the Board entity.
 *
 * @package Tests\ChessBundle\Domain\Model\Board
 */
class BoardTest extends TestCase
{
    /**
     * A knight that was never put on the board has no position and cannot move.
     */
    public function testKnightNotOnBoardHasNoPositionAndCannotMove()
    {
        $board = new Board(new BoardId('board'));
        $knight = new Knight(new KnightId('knight'));
        $move = new Move(Move::X_PLUS_1, Move::Y_PLUS_2);

        $this->assertNull($board->getPosition($knight));
        $this->assertFalse($board->checkCanMove($knight, $move));
        $this->assertFalse($board->moveTo($knight, $move));
    }

    /**
     * A placed knight moves to the expected square.
     */
    public function testPlacedKnightMoves()
    {
        $board = new Board(new BoardId('board'));
        $knight = new Knight(new KnightId('knight'));
        $board->putAt($knight, new Box(0));

        $this->assertTrue($board->moveTo($knight, new Move(Move::X_PLUS_1, Move::Y_PLUS_2)));
        $this->assertSame(17, $board->getPosition($knight)->getOneDimensionValue());
    }
}
