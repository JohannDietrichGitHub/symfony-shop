<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\OrderCreator;
use App\Service\StripeCheckout;
use Psr\Log\LoggerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout_start', methods: ['POST'])]
    public function start(
        Request $request,
        #[CurrentUser] User $user,
        OrderCreator $orderCreator,
        StripeCheckout $stripeCheckout,
        UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger,
    ): Response {
        if (!$this->isCsrfTokenValid('start_checkout', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        if (!$stripeCheckout->isConfigured()) {
            $this->addFlash(
                'error',
                'Stripe checkout is not configured. Set STRIPE_SECRET_KEY in .env.local, then restart the application.',
            );

            return $this->redirectToRoute('app_cart_show');
        }

        try {
            $order = $orderCreator->createFromCart($user);
            $checkoutUrl = $stripeCheckout->createSession(
                $order,
                $urlGenerator->generate('app_checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL)
                .'?session_id={CHECKOUT_SESSION_ID}',
                $urlGenerator->generate('app_cart_show', [], UrlGeneratorInterface::ABSOLUTE_URL),
            );
        } catch (\DomainException $exception) {
            $this->addFlash('error', $exception->getMessage());

            return $this->redirectToRoute('app_cart_show');
        } catch (ApiErrorException $exception) {
            $logger->error('Unable to create Stripe Checkout Session.', [
                'exception' => $exception,
            ]);
            $this->addFlash('error', 'Payment could not be started. Please try again.');

            return $this->redirectToRoute('app_cart_show');
        } catch (\RuntimeException $exception) {
            $logger->error('Checkout configuration is invalid.', [
                'exception' => $exception,
            ]);
            $this->addFlash(
                'error',
                'Stripe checkout is not configured. Set STRIPE_SECRET_KEY in .env.local, then restart the application.',
            );

            return $this->redirectToRoute('app_cart_show');
        }

        return $this->redirect($checkoutUrl);
    }

    #[Route('/checkout/success', name: 'app_checkout_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('checkout/success.html.twig');
    }
}
