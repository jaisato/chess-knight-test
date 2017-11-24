<?php

namespace Chess\Application\Knight;

use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\KnightId;

/**
 * Knight moves DTO.
 *
 * @package Chess\Application\Knight
 */
class KnightMovesDto
{
    /** @var string */
    public $knightId;

    /** @var int */
    public $source;

    /** @var int */
    public $destination;

    /** @var array */
    public $positions;

    /** @var array */
    public $totalMoves;

    /**
     * KnightMovesDto constructor.
     *
     * @param KnightId  $knightId      Knight identifier.
     * @param Box       $source        Source box.
     * @param Box       $destination   Destination box.
     * @param array     $movesSolution Array of solutions.
     */
    public function __construct(KnightId $knightId, Box $source, Box $destination, array $movesSolution)
    {
        $this->knightId = $knightId->id();
        $this->source = $source->getOneDimensionValue();
        $this->destination = $destination->getOneDimensionValue();

        $this->positions = array_map(
            function (Box $move) {
                return $move->getOneDimensionValue();
            },
            $movesSolution
        );

        $this->totalMoves = count($this->positions) - 1;
    }

    /**
     * Serializes DTO.
     *
     * @return string
     */
    public function serialize()
    {
        return json_encode($this);
    }
}