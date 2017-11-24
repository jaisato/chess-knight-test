<?php

namespace Chess\Domain\Model\Board;

use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\Move;

/**
 * Chess board entity.
 *
 * @package Chess\Persistence\Knight\Board
 */
class Board
{
    const NUMBER_OF_COLUMNS = 8;

    const NUMBER_OF_ROWS = 8;

    /** @var BoardId */
    private $boardId;

    /** @var Box[] Current position */
    private $position;

    /**
     * Board constructor.
     * @param BoardId $boardId
     */
    public function __construct(BoardId $boardId)
    {
        $this->boardId = $boardId;
    }

    /**
     * Gets board id.
     *
     * @return BoardId
     */
    public function id()
    {
        return $this->boardId;
    }

    /**
     * Starts position of knight piece at chess board.
     *
     * @param Knight $knight
     * @param Box $source
     */
    public function putAt(Knight $knight, Box $source)
    {
        $this->setPosition($knight, $source);
    }

    /**
     * Makes the given move for Knight piece on chess board as possible.
     *
     * @param Knight $knight     Knight piece.
     * @param Move   $knightMove Knight move.
     *
     * @return bool
     */
    public function moveTo(Knight $knight, Move $knightMove): bool
    {
        if (!$this->checkCanMove($knight, $knightMove)) {
            return false;
        }

        $newPosition = Box::createFromXYPosition(
            $this->getPosition($knight)->getX() + $knightMove->getX(),
            $this->getPosition($knight)->getY() + $knightMove->getY()
        );

        $this->setPosition($knight, $newPosition);

        return true;
    }

    /**
     * Checks if it's a valid move.
     *
     * @param Move     $knightMove      Knight move.
     * @param Box|null $currentPosition Current position of knight piece at board.
     *
     * @return bool
     */
    private function isAValidMove(Move $knightMove, ?Box $currentPosition): bool
    {
        if ($currentPosition === null) {
            return false;
        }

        try {
            Box::assertIsValid(
                $currentPosition->getX() + $knightMove->getX(),
                $currentPosition->getY() + $knightMove->getY()
            );
        } catch (InvalidBoxException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Gets Knight position at board.
     *
     * @param Knight $knight Knight piece.
     *
     * @return Box|null
     */
    public function getPosition(Knight $knight): ?Box
    {
        return $this->position[$knight->id()->id()];
    }

    /**
     * Sets Knight position at board.
     *
     * @param Knight $knight Knight.
     * @param Box    $box    Board box (position).
     */
    private function setPosition(Knight $knight, Box $box)
    {
        $this->position[$knight->id()->id()] = $box;
    }

    /**
     * Checks if Knight can make the given move.
     *
     * @param Knight $knight     Knight piece.
     * @param Move   $knightMove Knight's move.
     *
     * @return bool
     */
    public function checkCanMove(Knight $knight, Move $knightMove)
    {
        return $this->isAValidMove($knightMove, $this->getPosition($knight));
    }
}