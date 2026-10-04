<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\Move;

/**
 * Chess board entity.
 */
class Board
{
    public const int NUMBER_OF_COLUMNS = 8;

    public const int NUMBER_OF_ROWS = 8;

    /** @var array<string, Box> Current position of each knight, by knight id */
    private array $position = [];

    public function __construct(private readonly BoardId $boardId) {}

    /**
     * Gets board id.
     */
    public function id(): BoardId
    {
        return $this->boardId;
    }

    /**
     * Starts position of knight piece at chess board.
     */
    public function putAt(Knight $knight, Box $source): void
    {
        $this->setPosition($knight, $source);
    }

    /**
     * Makes the given move for Knight piece on chess board as possible.
     *
     * @param Knight $knight     knight piece
     * @param Move   $knightMove knight move
     *
     * @return bool whether the knight moved: false when it is not on the board or the move leaves it
     *
     * @throws InvalidBoxException
     */
    public function moveTo(Knight $knight, Move $knightMove): bool
    {
        $currentPosition = $this->getPosition($knight);

        if (null === $currentPosition || !$this->isAValidMove($knightMove, $currentPosition)) {
            return false;
        }

        $this->setPosition($knight, Box::createFromXYPosition(
            $currentPosition->getX() + $knightMove->getX(),
            $currentPosition->getY() + $knightMove->getY(),
        ));

        return true;
    }

    /**
     * Checks if it's a valid move.
     *
     * @param Move     $knightMove      knight move
     * @param Box|null $currentPosition current position of knight piece at board
     */
    private function isAValidMove(Move $knightMove, ?Box $currentPosition): bool
    {
        if (null === $currentPosition) {
            return false;
        }

        try {
            Box::assertIsValid(
                $currentPosition->getX() + $knightMove->getX(),
                $currentPosition->getY() + $knightMove->getY(),
            );
        } catch (InvalidBoxException) {
            return false;
        }

        return true;
    }

    /**
     * Gets Knight position at board.
     */
    public function getPosition(Knight $knight): ?Box
    {
        // A knight that was never put on the board has no position: answer
        // null as documented rather than read an undefined index (which
        // Symfony's debug error handler turns into an exception).
        return $this->position[$knight->id()->id()] ?? null;
    }

    /**
     * Sets Knight position at board.
     */
    private function setPosition(Knight $knight, Box $box): void
    {
        $this->position[$knight->id()->id()] = $box;
    }

    /**
     * Checks if Knight can make the given move.
     */
    public function checkCanMove(Knight $knight, Move $knightMove): bool
    {
        return $this->isAValidMove($knightMove, $this->getPosition($knight));
    }
}
