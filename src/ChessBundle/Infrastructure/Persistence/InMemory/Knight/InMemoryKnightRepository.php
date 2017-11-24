<?php

namespace Chess\Infrastructure\Persistence\InMemory\Knight;

use Chess\Domain\Model\Knight\InvalidKnightIdException;
use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Domain\Model\Knight\KnightRepository;
use Chess\Domain\Model\Knight\NotFoundKnightException;
use Ramsey\Uuid\Uuid;

/**
 * Class InMemoryKnightRepository
 *
 * @package Chess\Infrastructure\Persistence\InMemory\Knight
 */
class InMemoryKnightRepository implements KnightRepository
{
    /** @var Knight[] */
    private $knights = [];

    /**
     * Finds a knight by id.
     *
     * @param KnightId $knightId Knight identifier.
     *
     * @return Knight|null
     */
    public function ofId(KnightId $knightId): ?Knight
    {
        foreach ($this->knights as $aKnight) {
            if ($aKnight->id()->id() === $knightId->id()) {
                return $aKnight;
            }
        }

        return null;
    }

    /**
     * Finds a knight by id or fails (throws an exception).
     *
     * @param KnightId $knightId Knight identifier.
     *
     * @return Knight
     *
     * @throws NotFoundKnightException
     */
    public function ofIdOrFail(KnightId $knightId): Knight
    {
        $knight = $this->ofId($knightId);
        if (!$knight) {
            throw new NotFoundKnightException("Knight {$knightId->id()} not found");
        }

        return $knight;
    }

    /**
     * Generates a new Knight identity.
     *
     * @return KnightId
     *
     * @throws InvalidKnightIdException
     */
    public function newIdentity(): KnightId
    {
        return new KnightId(Uuid::uuid4()->toString());
    }

    /**
     * Adds a knight.
     *
     * @param Knight $knight Knight.
     *
     * @return Knight
     */
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

    /**
     * Removes a knight.
     *
     * @param Knight $knight Knight.
     *
     * @return Knight
     */
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