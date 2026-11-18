<?php

namespace App\Service;

use App\Entity\Order;
use App\Message\OrderPaidEmail;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentProcessor
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function markCheckoutAsPaid(Session $session): void
    {
        $order = $this->findOrder($session);

        if (null === $order || 'paid' === $order->getStatus()) {
            return;
        }

        $order
            ->setStripeCheckoutSessionId($session->id)
            ->setStripePaymentIntentId($this->paymentIntentId($session))
            ->markAsPaid(new \DateTimeImmutable());

        $this->removePurchasedQuantitiesFromCart($order);
        $this->entityManager->flush();
        $this->messageBus->dispatch(new OrderPaidEmail($order->getId()));
    }

    public function markCheckoutAsFailed(Session $session): void
    {
        $order = $this->findOrder($session);

        if (null === $order || 'paid' === $order->getStatus()) {
            return;
        }

        $order
            ->setStripeCheckoutSessionId($session->id)
            ->setStripePaymentIntentId($this->paymentIntentId($session))
            ->markPaymentFailed();

        $this->entityManager->flush();
    }

    private function findOrder(Session $session): ?Order
    {
        $orderId = $session->metadata->order_id ?? null;

        if (null === $orderId) {
            return null;
        }

        return $this->orderRepository->find((int) $orderId);
    }

    private function paymentIntentId(Session $session): ?string
    {
        return is_string($session->payment_intent) ? $session->payment_intent : null;
    }

    private function removePurchasedQuantitiesFromCart(Order $order): void
    {
        $cart = $order->getUser()?->getCart();

        if (null === $cart) {
            return;
        }

        $purchasedQuantities = [];

        foreach ($order->getOrderItems() as $orderItem) {
            $productId = $orderItem->getProduct()?->getId();

            if (null !== $productId) {
                $purchasedQuantities[$productId] = $orderItem->getQuantity();
            }
        }

        foreach ($cart->getCartItems() as $cartItem) {
            $productId = $cartItem->getProduct()?->getId();
            $purchasedQuantity = $purchasedQuantities[$productId] ?? 0;

            if ($purchasedQuantity < 1) {
                continue;
            }

            $remainingQuantity = $cartItem->getQuantity() - $purchasedQuantity;

            if ($remainingQuantity > 0) {
                $cartItem->setQuantity($remainingQuantity);
            } else {
                $cart->removeCartItem($cartItem);
                $this->entityManager->remove($cartItem);
            }

            unset($purchasedQuantities[$productId]);
        }

        $cart->setUpdatedAt(new \DateTimeImmutable());
    }
}
