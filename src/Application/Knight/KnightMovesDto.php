<?php

declare(strict_types=1);

namespace Chess\Application\Knight;

use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\KnightId;

/**
 * Knight moves DTO.
 */
class KnightMovesDto
{
    public readonly string $knightId;

    public readonly int $source;

    public readonly int $destination;

    /** @var list<string> */
    public readonly array $moves;

    public readonly int $totalMoves;

    /**
     * @param KnightId  $knightId        knight identifier
     * @param Box       $source          source box
     * @param Box       $destination     destination box
     * @param list<Box> $movesOfSolution path from source to destination (the source excluded)
     */
    public function __construct(KnightId $knightId, Box $source, Box $destination, array $movesOfSolution)
    {
        $this->knightId = $knightId->id();
        $this->source = $source->getOneDimensionValue();
        $this->destination = $destination->getOneDimensionValue();

        $this->moves = array_map(
            static fn(Box $move): string => "x: {$move->getX()} - y: {$move->getY()} ({$move->getOneDimensionValue()})",
            $movesOfSolution,
        );

        $this->totalMoves = \count($this->moves);
    }

    /**
     * Serializes DTO.
     */
    public function serialize(): string
    {
        // The knight id can come from the query string. Without the flag a
        // single invalid UTF-8 byte in it makes json_encode() fail, and the
        // page showed an empty solution.
        return json_encode($this, \JSON_THROW_ON_ERROR | \JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
