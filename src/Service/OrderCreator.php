<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class OrderCreator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function createFromCart(User $user): Order
    {
        $cart = $user->getCart();

        if (null === $cart || $cart->getCartItems()->isEmpty()) {
            throw new \DomainException('Cannot create an order from an empty cart.');
        }

        $order = new Order();
        $order
            ->setUser($user)
            ->setCurrency('eur')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setTotal(0);

        $total = 0;

        $this->entityManager->wrapInTransaction(function () use ($cart, $order, &$total): void {
            foreach ($cart->getCartItems() as $cartItem) {
                $product = $cartItem->getProduct();
                $quantity = $cartItem->getQuantity();

                if (null === $product || $product->isArchived()) {
                    throw new \DomainException('The cart contains an unavailable product.');
                }

                if (null === $quantity || $quantity < 1) {
                    throw new \DomainException('The cart contains an invalid quantity.');
                }

                if (null === $product->getPrice() || $product->getPrice() < 0) {
                    throw new \DomainException('The cart contains a product with an invalid price.');
                }

                if (null !== $product->getStock() && $quantity > $product->getStock()) {
                    throw new \DomainException(sprintf('Not enough stock for "%s".', $product->getName()));
                }

                $unitPrice = $product->getPrice();
                $total += $unitPrice * $quantity;

                $order->addOrderItem(
                    (new OrderItem())
                        ->setProduct($product)
                        ->setProductName((string) $product->getName())
                        ->setQuantity($quantity)
                        ->setUnitPrice($unitPrice),
                );
            }

            $order->setTotal($total);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        });

        return $order;
    }
}
