<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPasswordAction
{
    private $validator;
    private $passwordEncoder;
    private $entityManager;
    private $JWTTokenManager;

    public function __construct(ValidatorInterface $validator,
                                UserPasswordEncoderInterface $passwordEncoder,
                                EntityManagerInterface $entityManager,
                                JWTTokenManagerInterface $JWTTokenManager
                                )
    {
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    public function __invoke(User $data)  {

        /*dump($data->getNewPassword());
        dump($data->getNewRetypedPassword());
        dump($data->getOldPassword());
        dump($data->getRetypedPassword());
        die;*/
        //dump($this->validator->validate($data));die;

        $this->validator->validate($data);

        $data->setPassword(
            $this->passwordEncoder->encodePassword($data, $data->getNewPassword())
        );

        $data->setPasswordChangeDate(time());

        $this->entityManager->flush();

        $token = $this->JWTTokenManager->create($data);

        return new JsonResponse(['token' => $token]);

    }
}