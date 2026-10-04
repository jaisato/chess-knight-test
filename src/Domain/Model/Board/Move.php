<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

/**
 * Chess Move.
 */
class Move
{
    private readonly Box $source;

    private readonly Box $destination;

    /**
     * @param int $source      source board box
     * @param int $destination destination board box
     *
     * @throws InvalidBoxException
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
     */
    public function source(): Box
    {
        return $this->source;
    }

    /**
     * Gets destination.
     */
    public function destination(): Box
    {
        return $this->destination;
    }
}
