<?php

declare(strict_types=1);

namespace Chess\Tests\Infrastructure\UI\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests of Knight's controller.
 */
final class KnightControllerTest extends WebTestCase
{
    /**
     * Test Knight' controller action GetNumberOfMoves.
     */
    public function testGetNumberOfMoves(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/', ['source' => 0, 'destination' => 63]);

        static::assertResponseStatusCodeSame(200);
        static::assertStringContainsString('"totalMoves":6', $crawler->filter('#content b')->text());
    }

    /**
     * An off-board square is a client error, not a server error.
     */
    public function testOffBoardSourceIsABadRequest(): void
    {
        $client = static::createClient();

        $client->request('GET', '/', ['source' => 64, 'destination' => 63]);

        static::assertResponseStatusCodeSame(400);
    }

    /**
     * A square that is not an integer is a client error too. Symfony 3 read
     * `?source=abc` as square 0; InputBag::getInt() now refuses it.
     */
    public function testNonIntegerSourceIsABadRequest(): void
    {
        $client = static::createClient();

        $client->request('GET', '/?source=abc&destination=63');

        static::assertResponseStatusCodeSame(400);
    }

    /**
     * A board id the client names but that does not exist is a 404, not a 500.
     */
    public function testUnknownBoardIdIsNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/', ['boardId' => 'no-such-board', 'source' => 0, 'destination' => 63]);

        static::assertResponseStatusCodeSame(404);
    }

    /**
     * The same for a knight: the board is created on the fly, the knight is not.
     */
    public function testUnknownKnightIdIsNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/', ['knightId' => 'no-such-knight', 'source' => 0, 'destination' => 63]);

        static::assertResponseStatusCodeSame(404);
    }

    /**
     * `?knightId[]=x` is malformed input: 400, not a TypeError surfacing as 500.
     */
    public function testArrayKnightIdIsABadRequest(): void
    {
        $client = static::createClient();

        $client->request('GET', '/', ['knightId' => ['x'], 'source' => 0, 'destination' => 63]);

        static::assertResponseStatusCodeSame(400);
    }

    /**
     * Nothing may be written to the output ahead of the response: it would
     * send PHP's default headers first and the response's own would be lost.
     */
    public function testSolutionIsNotPrintedOutsideTheResponse(): void
    {
        $client = static::createClient();

        ob_start();
        $client->request('GET', '/', ['source' => 0, 'destination' => 63]);
        $printed = ob_get_clean();

        static::assertSame('', $printed);
        static::assertResponseStatusCodeSame(200);
    }
}
