<?php

namespace Chess\Domain\Model\Board;

/**
 * Chess Move.
 * @package Chess\Persistence\Knight\Board
 */
class Move
{
    /** @var Box */
    private $source;

    /** @var Box */
    private $destination;

    /**
     * Move constructor.
     *
     * @param int $source Source board box.
     * @param int $destination Destination board box.
     *
     * @throws InvalidMoveException
     */
    public function __construct(int $source, int $destination)
    {
        $this->source = new Box($source);
        $this->destination = new Box($destination);

        if ($this->source->equalsTo($this->destination)) {
            throw new InvalidMoveException();
        }
    }

    /**
     * Gets source.
     *
     * @return Box
     */
    public function source()
    {
        return $this->source;
    }

    /**
     * Gets destination.
     *
     * @return Box
     */
    public function destination()
    {
        return $this->destination;
    }
}