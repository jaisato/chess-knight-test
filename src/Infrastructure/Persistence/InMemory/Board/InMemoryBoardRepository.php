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
    /**
     * Keyed by id: one board per identity, found without walking the list.
     *
     * @var array<string, Board>
     */
    private array $boards = [];

    public function ofId(BoardId $boardId): ?Board
    {
        return $this->boards[$boardId->id()] ?? null;
    }

    public function ofIdOrFail(BoardId $boardId): Board
    {
        return $this->ofId($boardId) ?? throw new NotFoundBoardException("Board {$boardId->id()} not found");
    }

    public function newIdentity(): BoardId
    {
        return new BoardId(Uuid::uuid4()->toString());
    }

    /**
     * Adding an identity that is already tracked keeps the tracked board and
     * hands it back, so the caller goes on working with the instance the
     * repository holds. It used to answer the argument instead: a second
     * instance with the same id that nothing would ever find again.
     */
    public function add(Board $board): Board
    {
        return $this->boards[$board->id()->id()] ??= $board;
    }

    public function remove(Board $board): Board
    {
        unset($this->boards[$board->id()->id()]);

        return $board;
    }
}
