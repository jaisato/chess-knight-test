<?php

namespace Chess\Domain\Event\Knight;

use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\KnightId;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event for new Knight's shortest path found.
 *
 * @package Chess\Domain\Event\Knight
 */
class NewShortestPathFound extends Event
{
    /** @var KnightId */
    private $knightId;

    /** @var Box */
    private $origin;

    /** @var Box */
    private $destination;

    /** @var Box[] */
    private $solution;

    /**
     * NewShortestPathFound constructor.
     *
     * @param KnightId  $knightId    Knight id.
     * @param Box       $origin      Origin box.
     * @param Box       $destination Destination box.
     * @param array     $solution    Solution.
     */
    public function __construct(KnightId $knightId, Box $origin, Box $destination, array $solution)
    {
        $this->knightId = $knightId;
        $this->origin = $origin;
        $this->destination = $destination;
        $this->solution = $solution;
    }

    /**
     * Get Knight identifier.
     *
     * @return KnightId
     */
    public function knightId()
    {
        return $this->knightId;
    }

    /**
     * Get origin.
     *
     * @return Box
     */
    public function origin()
    {
        return $this->origin;
    }

    /**
     * Get destination.
     *
     * @return Box
     */
    public function destination()
    {
        return $this->destination;
    }

    /**
     * Get solution.
     *
     * @return array|Box[]
     */
    public function solution()
    {
        return $this->solution;
    }
}