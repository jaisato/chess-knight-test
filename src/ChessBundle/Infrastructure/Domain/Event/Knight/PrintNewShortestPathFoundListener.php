<?php

namespace Chess\Infrastructure\Domain\Event\Knight;

use Chess\Application\Knight\KnightMovesDto;
use Chess\Domain\Event\Knight\NewShortestPathFound;
use Psr\Log\LoggerInterface;

/**
 * Event listener for NewShortestPathFound event.
 *
 * @package Chess\Infrastructure\Domain\Event\Knight
 */
class PrintNewShortestPathFoundListener
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * PrintNewShortestPathFoundListener constructor.
     *
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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

        $this->logger->info('New shortest path found for knight {knightId}: {solution}', [
            'knightId' => $knightMovesDto->knightId,
            'solution' => $knightMovesDto->serialize(),
        ]);
    }
}
