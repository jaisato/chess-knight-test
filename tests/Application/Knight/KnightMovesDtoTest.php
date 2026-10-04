<?php

declare(strict_types=1);

namespace Chess\Tests\Application\Knight;

use Chess\Application\Knight\KnightMovesDto;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\KnightId;
use PHPUnit\Framework\TestCase;

/**
 * Tests of the KnightMovesDto.
 */
final class KnightMovesDtoTest extends TestCase
{
    /**
     * An invalid UTF-8 knight id (it can come from the query string) must not
     * make serialize() fail.
     */
    public function testSerializeSurvivesInvalidUtf8KnightId(): void
    {
        $dto = new KnightMovesDto(new KnightId("knight-\xB1"), new Box(0), new Box(17), [new Box(17)]);

        $decoded = json_decode($dto->serialize(), true, 512, \JSON_THROW_ON_ERROR);

        static::assertIsArray($decoded);
        static::assertSame(1, $decoded['totalMoves']);
        static::assertSame("knight-\u{FFFD}", $decoded['knightId']);
    }
}
