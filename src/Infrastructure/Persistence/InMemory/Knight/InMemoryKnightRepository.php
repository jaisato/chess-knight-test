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
    /** @var array<int, Knight> */
    private array $knights = [];

    public function ofId(KnightId $knightId): ?Knight
    {
        foreach ($this->knights as $aKnight) {
            if ($aKnight->id()->id() === $knightId->id()) {
                return $aKnight;
            }
        }

        return null;
    }

    public function ofIdOrFail(KnightId $knightId): Knight
    {
        return $this->ofId($knightId) ?? throw new NotFoundKnightException("Knight {$knightId->id()} not found");
    }

    public function newIdentity(): KnightId
    {
        return new KnightId(Uuid::uuid4()->toString());
    }

    public function add(Knight $knight): Knight
    {
        foreach ($this->knights as $aKnight) {
            if ($aKnight->id()->id() === $knight->id()->id()) {
                return $knight;
            }
        }

        $this->knights[] = $knight;

        return $knight;
    }

    public function remove(Knight $knight): Knight
    {
        foreach ($this->knights as $index => $aKnight) {
            if ($aKnight->id()->id() === $knight->id()->id()) {
                unset($this->knights[$index]);

                return $knight;
            }
        }

        return $knight;
    }
}
