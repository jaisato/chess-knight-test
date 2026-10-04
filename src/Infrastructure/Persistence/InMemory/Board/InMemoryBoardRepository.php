<?php

declare(strict_types=1);

namespace Chess\Infrastructure\Persistence\InMemory\Board;

use Chess\Domain\Model\Board\Board;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\NotFoundBoardException;
use Ramsey\Uuid\Uuid;

/**
 * Board repository that lives for the length of the request.
 */
class InMemoryBoardRepository implements BoardRepository
{
    /** @var array<int, Board> */
    private array $boards = [];

    public function ofId(BoardId $boardId): ?Board
    {
        foreach ($this->boards as $aBoard) {
            if ($aBoard->id()->id() === $boardId->id()) {
                return $aBoard;
            }
        }

        return null;
    }

    public function ofIdOrFail(BoardId $boardId): Board
    {
        return $this->ofId($boardId) ?? throw new NotFoundBoardException("Board {$boardId->id()} not found");
    }

    public function newIdentity(): BoardId
    {
        return new BoardId(Uuid::uuid4()->toString());
    }

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
