<?php

namespace App\Security;


use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmationService
{
    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function ConfirmUser(string $confirmationToken)
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        if(!$user) {
            throw new InvalidConfirmationTokenException();
        }
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
    }
}