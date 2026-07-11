<?php

namespace Chess\Infrastructure\Domain\Event\Knight;

use Chess\Application\Knight\KnightMovesDto;
use Chess\Domain\Event\Knight\NewShortestPathFound;

/**
 * Event listener for NewShortestPathFound event.
 *
 * @package Chess\Infrastructure\Domain\Event\Knight
 */
class PrintNewShortestPathFoundListener
{
    /**
     * Handles the events.
     *
     * @param NewShortestPathFound $event Event to be handled.
     */
    public function handle(NewShortestPathFound $event)
    {
        $knightMovesDto = new KnightMovesDto(
            $event->knightId(),
            $event->origin(),
            $event->destination(),
            $event->solution()
        );

        // The serialized DTO can contain user-supplied values (e.g. the knightId query
        // parameter, which is not validated). Escape it before emitting raw HTML to avoid
        // a reflected XSS vulnerability.
        $safeContent = htmlspecialchars(
            $knightMovesDto->serialize(),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        echo "<div><h4>New solution:</h4><p>{$safeContent}</p></div>";
    }
}