<?php

namespace Chess\Domain\Model\Knight;

/**
 * Knight identifier.
 *
 * @package Chess\Persistence\Knight\Knight
 */
class KnightId
{
    /** @var string */
    private $id;

    /**
     * KnightId constructor.
     *
     * @param string $id Knight id.
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Gets knight id value.
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }
}