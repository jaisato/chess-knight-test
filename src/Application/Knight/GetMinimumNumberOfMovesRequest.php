<?php

declare(strict_types=1);

namespace Chess\Application\Knight;

/**
 * Request of the GetMinimumNumberOfMovesService.
 */
class GetMinimumNumberOfMovesRequest
{
    /**
     * @param string|null $boardId     Board identifier. It may be null for pragmatic reasons (it won't in the real world).
     * @param string|null $knightId    Knight identifier. It may be null for pragmatic reasons (it won't in the real world).
     * @param int         $source      source position
     * @param int         $destination destination position
     */
    public function __construct(
        public readonly ?string $boardId,
        public readonly ?string $knightId,
        public readonly int $source,
        public readonly int $destination,
    ) {}
}
