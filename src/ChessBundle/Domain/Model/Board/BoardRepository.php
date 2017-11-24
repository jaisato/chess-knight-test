<?php

namespace Chess\Domain\Model\Board;

/**
 * Chess Board repository.
 *
 * @package Chess\Persistence\Knight\Board
 */
interface BoardRepository
{
    /**
     * Finds board by id.
     *
     * @param BoardId $boardId Board id.
     *
     * @return Board|null
     */
    public function ofId(BoardId $boardId): ?Board;

    /**
     * Finds board by id or fails (throws an exception).
     *
     * @param BoardId $boardId Board id.
     *
     * @return Board
     *
     * @throws NotFoundBoardException
     */
    public function ofIdOrFail(BoardId $boardId): Board;

    /**
     * Generates a new Board identity.
     *
     * @return BoardId
     *
     * @throws InvalidBoardIdException
     */
    public function newIdentity(): BoardId;

    /**
     * Adds a chess board.
     *
     * @param Board $board Chess board.
     *
     * @return Board
     */
    public function add(Board $board): Board;

    /**
     * Removes a chess board.
     *
     * @param Board $board Board Board.
     *
     * @return Board
     */
    public function remove(Board $board): Board;
}