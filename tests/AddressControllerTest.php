<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AddressControllerTest extends WebTestCase
{
    public function testAddressBookRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/profile/addresses');

        self::assertResponseRedirects('/login');
    }
}
