<?php

namespace Tests\ChessBundle\Application\Knight;

use Chess\Application\Knight\KnightMovesDto;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Knight\KnightId;
use PHPUnit\Framework\TestCase;

/**
 * Tests of the KnightMovesDto.
 *
 * @package Tests\ChessBundle\Application\Knight
 */
class KnightMovesDtoTest extends TestCase
{
    /**
     * An invalid UTF-8 knight id (it can come from the query string) must not
     * make serialize() return false.
     */
    public function testSerializeSurvivesInvalidUtf8KnightId()
    {
        $dto = new KnightMovesDto(new KnightId("knight-\xB1"), new Box(0), new Box(17), [new Box(17)]);

        $json = $dto->serialize();

        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertSame(1, $decoded['totalMoves']);
        $this->assertSame("knight-\u{FFFD}", $decoded['knightId']);
    }
}
