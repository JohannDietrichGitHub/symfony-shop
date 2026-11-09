<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testIndexProductPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Products', '');
    }
    public function testIndividualProductPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/products/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Mechanical Keyboard', '');
    }
    public function testUnknownProductReturns404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/products/999999');

        self::assertResponseStatusCodeSame(404);
    }
}
