<?php

namespace Chess\Domain\Model\Knight;

/**
 * Knight repository.
 *
 * @package Chess\Persistence\Knight\Knight
 */
interface KnightRepository
{
    /**
     * Finds a knight by id.
     *
     * @param KnightId $knightId Knight identifier.
     *
     * @return Knight|null
     */
    public function ofId(KnightId $knightId): ?Knight;

    /**
     * Finds a knight by id or fails (throws an exception).
     *
     * @param KnightId $knightId Knight identifier.
     *
     * @return Knight
     *
     * @throws NotFoundKnightException
     */
    public function ofIdOrFail(KnightId $knightId): Knight;

    /**
     * Generates a new Knight identity.
     *
     * @return KnightId
     *
     * @throws InvalidKnightIdException
     */
    public function newIdentity(): KnightId;

    /**
     * Adds a knight.
     *
     * @param Knight $knight Knight.
     *
     * @return Knight
     */
    public function add(Knight $knight): Knight;

    /**
     * Removes a knight.
     *
     * @param Knight $knight Knight.
     *
     * @return Knight
     */
    public function remove(Knight $knight): Knight;
}