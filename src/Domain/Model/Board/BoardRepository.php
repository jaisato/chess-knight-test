<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

/**
 * Chess Board repository.
 */
interface BoardRepository
{
    /**
     * Finds board by id.
     */
    public function ofId(BoardId $boardId): ?Board;

    /**
     * Finds board by id or fails (throws an exception).
     *
     * @throws NotFoundBoardException
     */
    public function ofIdOrFail(BoardId $boardId): Board;

    /**
     * Generates a new Board identity.
     *
     * @throws InvalidBoardIdException
     */
    public function newIdentity(): BoardId;

    /**
     * Adds a chess board.
     */
    public function add(Board $board): Board;

    /**
     * Removes a chess board.
     */
    public function remove(Board $board): Board;
}
