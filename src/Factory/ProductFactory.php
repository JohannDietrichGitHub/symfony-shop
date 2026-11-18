<?php

namespace App\Factory;

use App\Entity\Product;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Product>
 */
final class ProductFactory extends PersistentObjectFactory
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
        return Product::class;
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
            'createdAt' => new \DateTimeImmutable(),
            'description' => self::faker()->sentence(),
            'name' => self::faker()->unique()->words(3, true),
            'price' => self::faker()->numberBetween(100, 100_000),
            'stock' => self::faker()->numberBetween(0, 100),
            'updatedAt' => new \DateTimeImmutable(),
        ];
    }

    public function archived(): static
    {
        return $this->with([
            'archivedAt' => new \DateTimeImmutable(),
        ]);
    }

    public function inStock(int $stock = 10): static
    {
        return $this->with([
            'stock' => $stock,
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }
}
