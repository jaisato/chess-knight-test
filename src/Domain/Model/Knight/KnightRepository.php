<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

/**
 * Knight repository.
 */
interface KnightRepository
{
    /**
     * Finds a knight by id.
     */
    public function ofId(KnightId $knightId): ?Knight;

    /**
     * Finds a knight by id or fails (throws an exception).
     *
     * @throws NotFoundKnightException
     */
    public function ofIdOrFail(KnightId $knightId): Knight;

    /**
     * Generates a new Knight identity.
     *
     * @throws InvalidKnightIdException
     */
    public function newIdentity(): KnightId;

    /**
     * Adds a knight.
     */
    public function add(Knight $knight): Knight;

    /**
     * Removes a knight.
     */
    public function remove(Knight $knight): Knight;
}
