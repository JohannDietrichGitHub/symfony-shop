<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\CartItem;
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

    #[Route('/cart/items/{id}/increase', name: 'app_cart_item_increase', methods: ['POST'])]
    public function increaseQuantity(
        CartItem $cartItem,
        #[CurrentUser] User $user,
        Request $request,
        CartItemManager $cartItemManager,
    ): Response {
        $this->assertCartItemBelongsToUser($cartItem, $user);
        $this->assertValidCsrfToken($request, 'update_cart');
        $cartItemManager->changeQuantity($cartItem, 1);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/items/{id}/decrease', name: 'app_cart_item_decrease', methods: ['POST'])]
    public function decreaseQuantity(
        CartItem $cartItem,
        #[CurrentUser] User $user,
        Request $request,
        CartItemManager $cartItemManager,
    ): Response {
        $this->assertCartItemBelongsToUser($cartItem, $user);
        $this->assertValidCsrfToken($request, 'update_cart');
        $cartItemManager->changeQuantity($cartItem, -1);

        return $this->redirectToRoute('app_cart_show');
    }

    private function assertCartItemBelongsToUser(CartItem $cartItem, User $user): void
    {
        if ($cartItem->getCart()?->getCustomer() !== $user) {
            throw $this->createAccessDeniedException();
        }
    }

    private function assertValidCsrfToken(Request $request, string $tokenId): void
    {
        if (!$this->isCsrfTokenValid($tokenId, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }
    }
}
