<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

/**
 * Knight identifier.
 */
class KnightId
{
    public function __construct(private readonly string $id) {}

    /**
     * Gets knight id value.
     */
    public function id(): string
    {
        return $this->id;
    }
}
