<?php

declare(strict_types=1);

namespace Chess\Tests\Infrastructure\UI\Web\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests of Knight's controller.
 */
final class KnightControllerTest extends WebTestCase
{
    /**
     * Squares that InputBag::getInt() refuses: its FILTER_VALIDATE_INT only
     * takes a decimal integer without leading zeros. Symfony 3 cast any of
     * them to int and answered 200 (`abc`, `''` and `0x1F` were square 0,
     * `05` square 5, `[1]` square 1).
     *
     * @return iterable<string, array{array<string, string|list<string>>}>
     */
    public static function malformedSquareProvider(): iterable
    {
        yield 'source that is not a number' => [['source' => 'abc']];
        yield 'empty source' => [['source' => '']];
        yield 'source with a leading zero' => [['source' => '05']];
        yield 'decimal source' => [['source' => '1.0']];
        yield 'source in exponent notation' => [['source' => '1e1']];
        yield 'hexadecimal source' => [['source' => '0x1F']];
        yield 'source as an array' => [['source' => ['1']]];
        yield 'empty destination' => [['destination' => '']];
        yield 'decimal destination' => [['destination' => '1.5']];
        yield 'destination as an array' => [['destination' => ['1']]];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function notAllowedMethodProvider(): iterable
    {
        foreach (['POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'] as $method) {
            yield $method => [$method];
        }
    }

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
     * A square that is not a decimal integer without leading zeros is a
     * client error too, and so is an empty one.
     *
     * @param array<string, string|list<string>> $query
     */
    #[DataProvider('malformedSquareProvider')]
    public function testMalformedSquareIsABadRequest(array $query): void
    {
        $client = static::createClient();

        $client->request('GET', '/', $query + ['source' => '0', 'destination' => '63']);

        static::assertResponseStatusCodeSame(400);
    }

    /**
     * The route only answers GET, and HEAD, which the router matches as GET.
     * Symfony 3 answered any method with the solution.
     */
    #[DataProvider('notAllowedMethodProvider')]
    public function testOtherMethodsAreNotAllowed(string $method): void
    {
        $client = static::createClient();

        $client->request($method, '/?source=0&destination=63');

        static::assertResponseStatusCodeSame(405);
    }

    public function testHeadIsAnsweredLikeGet(): void
    {
        $client = static::createClient();

        $client->request('HEAD', '/?source=0&destination=63');

        static::assertResponseStatusCodeSame(200);
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
