<?php

namespace App\Controller;

use App\Service\OrderPaymentProcessor;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StripeWebhookController
{
    public function __construct(
        #[Autowire('%env(STRIPE_WEBHOOK_SECRET)%')]
        private string $webhookSecret,
    ) {
    }

    #[Route('/webhooks/stripe', name: 'app_stripe_webhook', methods: ['POST'])]
    public function __invoke(Request $request, OrderPaymentProcessor $processor): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->headers->get('Stripe-Signature', ''),
                $this->webhookSecret,
            );
        } catch (\UnexpectedValueException|SignatureVerificationException) {
            return new Response('Invalid webhook payload.', Response::HTTP_BAD_REQUEST);
        }

        $session = $event->data->object;

        if ($session instanceof \Stripe\Checkout\Session) {
            match ($event->type) {
                'checkout.session.completed' => 'paid' === $session->payment_status
                    ? $processor->markCheckoutAsPaid($session)
                    : null,
                'checkout.session.async_payment_succeeded' => $processor->markCheckoutAsPaid($session),
                'checkout.session.async_payment_failed' => $processor->markCheckoutAsFailed($session),
                default => null,
            };
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
