<?php

declare(strict_types=1);

namespace Chess\Domain\Event\Knight;

use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\KnightId;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event for new Knight's shortest path found.
 */
class NewShortestPathFound extends Event
{
    /**
     * @param KnightId  $knightId    knight id
     * @param Box       $origin      origin box
     * @param Box       $destination destination box
     * @param list<Box> $solution    path from origin to destination (the origin excluded)
     */
    public function __construct(
        private readonly KnightId $knightId,
        private readonly Box $origin,
        private readonly Box $destination,
        private readonly array $solution,
    ) {}

    /**
     * Get Knight identifier.
     */
    public function knightId(): KnightId
    {
        return $this->knightId;
    }

    /**
     * Get origin.
     */
    public function origin(): Box
    {
        return $this->origin;
    }

    /**
     * Get destination.
     */
    public function destination(): Box
    {
        return $this->destination;
    }

    /**
     * Get solution.
     *
     * @return list<Box>
     */
    public function solution(): array
    {
        return $this->solution;
    }
}
