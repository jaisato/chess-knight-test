<?php

declare(strict_types=1);

namespace Chess\Infrastructure\Persistence\InMemory\Knight;

use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Domain\Model\Knight\KnightRepository;
use Chess\Domain\Model\Knight\NotFoundKnightException;
use Ramsey\Uuid\Uuid;

/**
 * Knight repository that lives for the length of the request.
 */
class InMemoryKnightRepository implements KnightRepository
{
    /**
     * Keyed by id: one knight per identity, found without walking the list.
     *
     * @var array<string, Knight>
     */
    private array $knights = [];

    public function ofId(KnightId $knightId): ?Knight
    {
        return $this->knights[$knightId->id()] ?? null;
    }

    public function ofIdOrFail(KnightId $knightId): Knight
    {
        return $this->ofId($knightId) ?? throw new NotFoundKnightException("Knight {$knightId->id()} not found");
    }

    public function newIdentity(): KnightId
    {
        return new KnightId(Uuid::uuid4()->toString());
    }

    /**
     * Adding an identity that is already tracked keeps the tracked knight and
     * hands it back, so the caller goes on working with the instance the
     * repository holds. It used to answer the argument instead: a second
     * instance with the same id that nothing would ever find again.
     */
    public function add(Knight $knight): Knight
    {
        return $this->knights[$knight->id()->id()] ??= $knight;
    }

    public function remove(Knight $knight): Knight
    {
        unset($this->knights[$knight->id()->id()]);

        return $knight;
    }
}
