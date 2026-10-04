<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

/**
 * Chess Board Identifier.
 */
class BoardId
{
    public function __construct(private readonly string $id) {}

    /**
     * Gets Board id value.
     */
    public function id(): string
    {
        return $this->id;
    }
}
