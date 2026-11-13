<?php

namespace App\Controller;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products_list', methods: ['GET'])]
    public function showProducts(ProductRepository $productRepository)
    {
        $products = $productRepository->findBy(
            [],
            ['id' => 'ASC'],
            25
        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/products/{id}', name: 'app_product_show', methods: ['GET'])]
    public function showProduct(
        int $id,
        ProductRepository $productRepository
    ) {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
