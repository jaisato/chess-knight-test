<?php

namespace Chess\Application\Knight;

use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\KnightId;

/**
 * Knight moves DTO.
 *
 * @package Chess\Domain\Knight
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
    public $moves;

    /** @var array */
    public $totalMoves;

    /**
     * KnightMovesDto constructor.
     *
     * @param KnightId  $knightId        Knight identifier.
     * @param Box       $source          Source box.
     * @param Box       $destination     Destination box.
     * @param array     $movesOfSolution Array of solutions.
     */
    public function __construct(KnightId $knightId, Box $source, Box $destination, array $movesOfSolution)
    {
        $this->knightId = $knightId->id();
        $this->source = $source->getOneDimensionValue();
        $this->destination = $destination->getOneDimensionValue();

        $this->moves = array_map(
            function (Box $move) {
                return "x: {$move->getX()} - y: {$move->getY()} ({$move->getOneDimensionValue()})";
            },
            $movesOfSolution
        );

        $this->totalMoves = count($this->moves);
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