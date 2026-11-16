<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CartManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getOrCreateCart(User $user): Cart
    {
        if (null !== ($cart = $user->getCart())) {
            return $cart;
        }

        $now = new \DateTimeImmutable();
        $cart = (new Cart())
            ->setCustomer($user)
            ->setCreatedAt($now)
            ->setUpdatedAt($now);

        $user->setCart($cart);
        $this->entityManager->persist($cart);

        return $cart;
    }
}
