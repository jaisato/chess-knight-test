<?php

declare(strict_types=1);

namespace Chess\Tests\Infrastructure\Domain\Event\Knight;

use Chess\Domain\Event\Knight\NewShortestPathFound;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Infrastructure\Domain\Event\Knight\LogNewShortestPathFoundListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests of the listener that records every new shortest path.
 */
final class LogNewShortestPathFoundListenerTest extends TestCase
{
    /**
     * The solution goes to the log, never to the output (see KnightControllerTest).
     */
    public function testTheSolutionIsLogged(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(static::once())
            ->method('info')
            ->with(
                'New shortest path found for knight {knightId}: {solution}',
                static::callback(static fn(array $context): bool => 'knight' === ($context['knightId'] ?? null)
                    && \is_string($context['solution'] ?? null)
                    && str_contains($context['solution'], '"totalMoves":1')),
            );

        $listener = new LogNewShortestPathFoundListener($logger);

        $this->expectOutputString('');
        $listener->handle(new NewShortestPathFound(new KnightId('knight'), new Box(0), new Box(17), [new Box(17)]));
    }
}
