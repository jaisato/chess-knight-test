<?php

namespace Chess\Application\Knight;

/**
 * Class GetMinimumNumberOfMovesRequest
 */
class GetMinimumNumberOfMovesRequest
{
    /** @var string|null */
    public $boardId;

    /** @var string|null */
    public $knightId;

    /** @var int */
    public $source;

    /** @var int */
    public $destination;

    /**
     * GetMinimumNumberOfMovesRequest constructor.
     *
     * @param string|null $boardId     Board identifier. It may be null for pragmatic reasons (it won't in the real-world).
     * @param string|null $knightId    Knight identifier. It may be null for pragmatic reasons (it won't in the real-world).
     * @param int         $source      Source position.
     * @param int         $destination Destination position.
     */
    public function __construct(?string $boardId, ?string $knightId, int $source, int $destination)
    {
        $this->boardId     = $boardId;
        $this->knightId    = $knightId;
        $this->source      = $source;
        $this->destination = $destination;
    }
}