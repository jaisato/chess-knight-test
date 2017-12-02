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

        print_r("<div><h4>New solution:</h4><p>{$knightMovesDto->serialize()}</p></div>", false);
    }
}