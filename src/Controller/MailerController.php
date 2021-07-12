<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailerController extends AbstractController
{
    /**
     * @Route("/email", name="email_new")
     */
    public function sendEmail(MailerInterface $mailer, User $user, string $password): Response
    {
        $email = (new TemplatedEmail())
            ->from('register@unblockme.com')
            ->to($user->getUserIdentifier())
            ->subject('New account register')
            ->htmlTemplate('mailer/email.html.twig')

            ->context([
                'username' => $user->getUserIdentifier(),
                'password' => $password,
            ]);

        $mailer->send($email);

        return $this->redirectToRoute('app_login');
    }
}