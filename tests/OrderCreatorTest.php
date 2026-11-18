<?php

namespace App\Tests;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\OrderCreator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class OrderCreatorTest extends TestCase
{
    public function testCreatesAnOrderUsingDatabaseProductPrices(): void
    {
        $product = (new Product())
            ->setName('Keyboard')
            ->setDescription('Test product')
            ->setPrice(8999)
            ->setStock(5);
        $cart = $this->cartWithProduct($product, 2);
        $entityManager = $this->entityManager();
        $order = (new OrderCreator($entityManager, $this->orderRepository()))->createFromCart($cart->getCustomer());

        self::assertSame(17998, $order->getTotal());
        self::assertSame('eur', $order->getCurrency());
        self::assertSame('Keyboard', $order->getOrderItems()->first()->getProductName());
        self::assertSame(8999, $order->getOrderItems()->first()->getUnitPrice());
    }

    public function testRejectsArchivedProducts(): void
    {
        $product = (new Product())
            ->setName('Archived product')
            ->setDescription('Test product')
            ->setPrice(100)
            ->setStock(5)
            ->archive();
        $cart = $this->cartWithProduct($product, 1);

        $this->expectException(\DomainException::class);
        (new OrderCreator($this->entityManager(), $this->orderRepository()))->createFromCart($cart->getCustomer());
    }

    private function cartWithProduct(Product $product, int $quantity): Cart
    {
        $user = (new User())->setEmail('customer@example.test');
        $cart = (new Cart())
            ->setCustomer($user)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
        $user->setCart($cart);
        $cart->addCartItem(
            (new CartItem())
                ->setProduct($product)
                ->setQuantity($quantity)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable()),
        );

        return $cart;
    }

    private function entityManager(): EntityManagerInterface
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('wrapInTransaction')
            ->willReturnCallback(static function (callable $callback): mixed {
                return $callback();
            });

        return $entityManager;
    }

    private function orderRepository(): OrderRepository
    {
        $repository = $this->createMock(OrderRepository::class);
        $repository->method('findLatestPendingForUser')->willReturn(null);

        return $repository;
    }
}
