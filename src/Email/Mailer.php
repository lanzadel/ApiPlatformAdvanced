<?php

namespace App\Email;

use App\Entity\User;
use Swift_Message;
use Twig_Environment;

class Mailer
{
    private $mailer;
    private $twig;
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendConfirmationEmail(User $user) {

        $body = $this->twig->render(
            'email/confirmation.html.twig',
            [
                'user' => $user
            ]
        );

        $message = (new Swift_Message('Please confirm your account!'))
            ->setForm("mine@gmail.com")
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);


    }
}