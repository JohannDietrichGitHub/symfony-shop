<?php

namespace App\Factory;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<OrderItem>
 */
final class OrderItemFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return OrderItem::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'order' => OrderFactory::new(),
            'product' => ProductFactory::new(),
            'productName' => self::faker()->words(3, true),
            'quantity' => self::faker()->numberBetween(1, 3),
            'unitPrice' => self::faker()->numberBetween(100, 100_000),
        ];
    }

    public function forOrder(Order|Proxy $order): static
    {
        return $this->with([
            'order' => $order,
        ]);
    }

    public function forProduct(Product|Proxy $product): static
    {
        return $this->with([
            'product' => $product,
            'productName' => $product->getName(),
            'unitPrice' => $product->getPrice(),
        ]);
    }

    public function withQuantity(int $quantity): static
    {
        return $this->with([
            'quantity' => $quantity,
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(OrderItem $orderItem): void {})
        ;
    }
}
