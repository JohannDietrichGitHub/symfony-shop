<?php

namespace App\Tests;

use App\Factory\AddressFactory;
use App\Factory\CartFactory;
use App\Factory\CartItemFactory;
use App\Factory\OrderFactory;
use App\Factory\OrderItemFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\Factory\UserProfileFactory;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

final class FactoryTest extends KernelTestCase
{
    #[Test]
    #[ResetDatabase]
    public function allEntityFactoriesCanPersistTheirRelationships(): void
    {
        $user = UserFactory::createOne();
        $product = ProductFactory::createOne();
        $cart = CartFactory::createOne();

        CartItemFactory::createOne([
            'cart' => $cart,
            'product' => $product,
        ]);
        AddressFactory::createOne(['user' => $user]);
        UserProfileFactory::createOne(['user' => $user]);

        $order = OrderFactory::createOne(['user' => $user]);
        OrderItemFactory::createOne([
            'order' => $order,
            'product' => $product,
            'productName' => $product->getName(),
            'unitPrice' => $product->getPrice(),
        ]);

        self::assertNotNull($user->getId());
        self::assertNotNull($product->getId());
        self::assertNotNull($cart->getId());
        self::assertNotNull($order->getId());
    }
}
