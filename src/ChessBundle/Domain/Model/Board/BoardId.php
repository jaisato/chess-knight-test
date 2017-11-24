<?php

namespace Chess\Domain\Model\Board;

/**
 * Chess Board Identifier.
 *
 * @package Chess\Persistence\Knight\Board
 */
class BoardId
{
    /** @var string */
    private $id;

    /**
     * BoardId constructor.
     *
     * @param string $id Board id.
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Gets Board id value.
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }
}