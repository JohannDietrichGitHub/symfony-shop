<?php

namespace App\Factory;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<CartItem>
 */
final class CartItemFactory extends PersistentObjectFactory
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
        return CartItem::class;
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
            'cart' => CartFactory::new(),
            'createdAt' => new \DateTimeImmutable(),
            'product' => ProductFactory::new(),
            'quantity' => self::faker()->numberBetween(1, 3),
            'updatedAt' => new \DateTimeImmutable(),
        ];
    }

    public function forCart(Cart|Proxy $cart): static
    {
        return $this->with([
            'cart' => $cart,
        ]);
    }

    public function forProduct(Product|Proxy $product): static
    {
        return $this->with([
            'product' => $product,
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
            // ->afterInstantiate(function(CartItem $cartItem): void {})
        ;
    }
}
