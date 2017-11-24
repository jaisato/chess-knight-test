<?php

namespace Tests\ChessBundle\Infrastructure\UI\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class KnightControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}
