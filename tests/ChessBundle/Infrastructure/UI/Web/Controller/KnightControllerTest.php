<?php

namespace Tests\ChessBundle\Infrastructure\UI\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests of Knight's controller.
 *
 * @package Tests\ChessBundle\Infrastructure\UI\Web\Controller
 */
class KnightControllerTest extends WebTestCase
{
    /**
     * Test Knight' controller action GetNumberOfMoves.
     */
    public function testGetNumberOfMoves()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/', ['source' => 0, 'destination' => 63]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('"totalMoves":6', $crawler->filter('#content b')->text());
    }
}
