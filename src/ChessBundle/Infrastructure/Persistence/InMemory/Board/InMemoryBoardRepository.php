<?php

namespace Chess\Infrastructure\Persistence\InMemory\Board;

use Chess\Domain\Model\Board\Board;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\InvalidBoardIdException;
use Chess\Domain\Model\Board\NotFoundBoardException;
use Ramsey\Uuid\Uuid;

/**
 * Class InMemoryBoardRepository
 *
 * @package Chess\Infrastructure\Persistence\InMemory\Board
 */
class InMemoryBoardRepository implements BoardRepository
{
    /** @var Board[] */
    private $boards = [];

    /**
     * Finds board by id.
     *
     * @param BoardId $boardId Board id.
     *
     * @return Board|null
     */
    public function ofId(BoardId $boardId): ?Board
    {
        foreach ($this->boards as $aBoard) {
            if ($aBoard->id()->id() === $boardId->id()) {
                return $aBoard;
            }
        }

        return null;
    }

    /**
     * Finds board by id or fails (throws an exception).
     *
     * @param BoardId $boardId Board id.
     *
     * @return Board
     *
     * @throws NotFoundBoardException
     */
    public function ofIdOrFail(BoardId $boardId): Board
    {
        $board = $this->ofId($boardId);
        if (!$board) {
            throw new NotFoundBoardException("Board {$boardId->id()} not found");
        }

        return $board;
    }

    /**
     * Generates a new Board identity.
     *
     * @return BoardId
     *
     * @throws InvalidBoardIdException
     */
    public function newIdentity(): BoardId
    {
        return new BoardId(Uuid::uuid4()->toString());
    }

    /**
     * Adds a chess board.
     *
     * @param Board $board Chess board.
     *
     * @return Board
     */
    public function add(Board $board): Board
    {
        foreach ($this->boards as $aBoard) {
            if ($aBoard->id()->id() === $board->id()->id()) {
                return $board;
            }
        }

        $this->boards[] = $board;

        return $board;
    }

    /**
     * Removes a chess board.
     *
     * @param Board $board Board Board.
     *
     * @return Board
     */
    public function remove(Board $board): Board
    {
        foreach ($this->boards as $index => $aBoard) {
            if ($aBoard->id()->id() === $board->id()->id()) {
                unset($this->boards[$index]);
                return $board;
            }
        }

        return $board;
    }
}