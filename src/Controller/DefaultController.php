<?php

namespace App\Controller;

use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/b",name="default_index")
     */
    public function index() {
        return $this->render(
            'base.html.twig'
        );
    }
    /**
     * @Route("/confirm-email/{token}", name="default_confirm_token")
     */
    public function confirmUser(string $token, UserConfirmationService $confirmationService) {
        $confirmationService->confirmUser($token);

        return $this->redirectToRoute('default_index');
    }

}