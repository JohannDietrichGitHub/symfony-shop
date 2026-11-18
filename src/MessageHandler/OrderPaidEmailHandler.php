<?php

namespace App\MessageHandler;

use App\Message\OrderPaidEmail;
use App\Repository\OrderRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

#[AsMessageHandler]
final class OrderPaidEmailHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
        private MailerInterface $mailer,
        private Environment $twig,
        #[Autowire('%env(MAILER_FROM_ADDRESS)%')]
        private string $fromAddress,
    ) {
    }

    public function __invoke(OrderPaidEmail $message): void
    {
        $order = $this->orderRepository->find($message->orderId);

        if (null === $order || null === $order->getUser()?->getEmail()) {
            return;
        }

        $this->mailer->send(
            (new Email())
                ->from($this->fromAddress)
                ->to($order->getUser()->getEmail())
                ->subject(sprintf('Order #%d confirmed', $order->getId()))
                ->html($this->twig->render('emails/order_paid.html.twig', [
                    'order' => $order,
                ])),
        );
    }
}
