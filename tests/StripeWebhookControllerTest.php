<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class StripeWebhookControllerTest extends WebTestCase
{
    public function testRejectsAnUnsignedPayload(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/webhooks/stripe',
            server: ['HTTP_STRIPE_SIGNATURE' => 'invalid'],
            content: '{}',
        );

        self::assertResponseStatusCodeSame(400);
    }
}
