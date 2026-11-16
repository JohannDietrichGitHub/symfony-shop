<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Service\CartItemManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart_show', methods: ['GET'])]
    public function showCart(#[CurrentUser] User $user): Response
    {
        return $this->render('cart/cart.html.twig', [
            'userCart' => $user->getCart(),
        ]);
    }

    #[Route('/cart/items/{id}', name: 'app_cart_item_add', methods: ['POST'])]
    public function addToCart(
        Product $product,
        #[CurrentUser] User $user,
        Request $request,
        CartItemManager $cartItemManager,
    ): Response
    {
        if (!$this->isCsrfTokenValid('add_to_cart', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $cartItemManager->addProduct($user, $product);

        return $this->redirectToRoute('app_cart_show');
    }
}
