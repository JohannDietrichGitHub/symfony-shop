<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\AddressType;
use App\Service\AddressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/profile/addresses', name: 'app_address_')]
final class AddressController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(AddressService $addressService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('address/index.html.twig', [
            'addresses' => $addressService->findForUser($user),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, AddressService $addressService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressService->save($address, $user);

            return $this->redirectToRoute('app_address_index');
        }

        return $this->render('address/form.html.twig', [
            'addressForm' => $form,
            'heading' => 'Add an address',
            'submitLabel' => 'Save address',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, AddressService $addressService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $address = $addressService->findOneForUser($user, $id);
        if (!$address) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressService->save($address, $user);

            return $this->redirectToRoute('app_address_index');
        }

        return $this->render('address/form.html.twig', [
            'addressForm' => $form,
            'heading' => 'Edit address',
            'submitLabel' => 'Save changes',
        ]);
    }

    #[Route('/{id}/default', name: 'default', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function setDefault(int $id, Request $request, AddressService $addressService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $address = $addressService->findOneForUser($user, $id);
        if (!$address) {
            throw $this->createNotFoundException();
        }

        if ($this->isCsrfTokenValid('default-address-'.$address->getId(), $request->request->get('_token'))) {
            $address->setDefault(true);
            $addressService->save($address, $user);
        }

        return $this->redirectToRoute('app_address_index');
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(int $id, Request $request, AddressService $addressService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $address = $addressService->findOneForUser($user, $id);
        if (!$address) {
            throw $this->createNotFoundException();
        }

        if ($this->isCsrfTokenValid('delete-address-'.$address->getId(), $request->request->get('_token'))) {
            $addressService->delete($address);
        }

        return $this->redirectToRoute('app_address_index');
    }
}
