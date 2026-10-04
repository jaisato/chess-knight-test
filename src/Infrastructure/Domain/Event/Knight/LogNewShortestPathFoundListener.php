<?php

declare(strict_types=1);

namespace Chess\Infrastructure\Domain\Event\Knight;

use Chess\Application\Knight\KnightMovesDto;
use Chess\Domain\Event\Knight\NewShortestPathFound;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Event listener for NewShortestPathFound event.
 */
class LogNewShortestPathFoundListener
{
    public function __construct(private readonly LoggerInterface $logger) {}

    /**
     * Handles the events.
     *
     * The solution used to be print_r()'d from here. The listener runs while
     * the controller is still building its Response, so that output reached
     * the client before Symfony had sent the status line and headers: PHP
     * flushed its own defaults, Response::sendHeaders() then found
     * headers_sent() and silently dropped every header of the real response,
     * and the markup landed in front of the page's doctype. The page itself
     * already renders the solution, so the event is recorded in the log.
     */
    #[AsEventListener]
    public function handle(NewShortestPathFound $event): void
    {
        $knightMovesDto = new KnightMovesDto(
            $event->knightId(),
            $event->origin(),
            $event->destination(),
            $event->solution(),
        );

        $this->logger->info('New shortest path found for knight {knightId}: {solution}', [
            'knightId' => $knightMovesDto->knightId,
            'solution' => $knightMovesDto->serialize(),
        ]);
    }
}
