<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CartItemManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartManager $cartManager,
    ) {
    }

    public function addProduct(User $user, Product $product): void
    {
        $cart = $this->cartManager->getOrCreateCart($user);
        $now = new \DateTimeImmutable();

        foreach ($cart->getCartItems() as $cartItem) {
            if ($cartItem->getProduct() === $product) {
                $cartItem->setQuantity(($cartItem->getQuantity() ?? 0) + 1);
                $cartItem->setUpdatedAt($now);
                $cart->setUpdatedAt($now);
                $this->entityManager->flush();

                return;
            }
        }

        $cartItem = (new CartItem())
            ->setProduct($product)
            ->setQuantity(1)
            ->setCreatedAt($now)
            ->setUpdatedAt($now);

        $cart->addCartItem($cartItem);
        $cart->setUpdatedAt($now);

        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    public function changeQuantity(CartItem $cartItem, int $amount): void
    {
        $cart = $cartItem->getCart();
        $quantity = ($cartItem->getQuantity() ?? 0) + $amount;

        if (null === $cart) {
            return;
        }

        if ($quantity <= 0) {
            $cart->removeCartItem($cartItem);
            $this->entityManager->remove($cartItem);
        } else {
            $cartItem->setQuantity($quantity);
            $cartItem->setUpdatedAt(new \DateTimeImmutable());
        }

        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }
}
