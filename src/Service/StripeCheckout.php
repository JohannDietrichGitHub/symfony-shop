<?php

namespace App\Service;

use App\Entity\Order;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class StripeCheckout
{
    public function __construct(
        #[Autowire('%env(STRIPE_SECRET_KEY)%')]
        private string $secretKey,
    ) {
    }

    public function isConfigured(): bool
    {
        return '' !== $this->secretKey;
    }

    public function createSession(Order $order, string $successUrl, string $cancelUrl): string
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('STRIPE_SECRET_KEY is not configured.');
        }

        $lineItems = [];

        foreach ($order->getOrderItems() as $orderItem) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $order->getCurrency(),
                    'product_data' => [
                        'name' => $orderItem->getProductName(),
                    ],
                    'unit_amount' => $orderItem->getUnitPrice(),
                ],
                'quantity' => $orderItem->getQuantity(),
            ];
        }

        $session = (new StripeClient($this->secretKey))->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'customer_email' => $order->getUser()?->getEmail(),
            'client_reference_id' => (string) $order->getId(),
            'metadata' => [
                'order_id' => (string) $order->getId(),
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ], [
            'idempotency_key' => sprintf('order-checkout-%d', $order->getId()),
        ]);

        return $session->url;
    }
}
