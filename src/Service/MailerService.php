<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailerService
{
    private MailerInterface $mailer;
    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/email", name="app_mailer")
     * @throws TransportExceptionInterface
     */
    public function sendEmail(User $user, string $password)
    {
        $email = (new TemplatedEmail())
            ->from('register@unblockme.com')
            ->to($user->getUserIdentifier())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Thanks for signing up!')
            ->htmlTemplate('mailer/index.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'username' => $user->getUserIdentifier(),
                'password' => $password,
            ]);

        $this->mailer->send($email);
    }

    /**
     * @param User $blocker
     * @param User $blockee
     * @param string $lp
     * @throws TransportExceptionInterface
     */
    public function sendBlockeeReport(User $blocker, User $blockee, string $lp)
    {
        $email = (new TemplatedEmail())
            ->from('register@unblockme.com')
            ->to($blockee->getUserIdentifier())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Report')
            ->htmlTemplate('mailer/BlockeeReport.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'blocker' => $blocker->getUserIdentifier(),
                'blocker_lp' => $lp,
            ]);

        $this->mailer->send($email);
    }

    /**
     * @param User $blockee
     * @param User $blocker
     * @param string $license_plate
     * @throws TransportExceptionInterface
     */
    public function sendBlockerReport(User $blockee, User $blocker, string $license_plate)
    {
        $email = (new TemplatedEmail())
            ->from('register@unblockme.com')
            ->to($blocker->getUserIdentifier())
            ->subject('Report')
            ->htmlTemplate('mailer/BlockerReport.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'blockee' => $blockee->getUserIdentifier(),
                'blockee_lp' => $license_plate,
            ]);

        $this->mailer->send($email);
    }

    /**
     * @param User $user
     * @param string $password
     * @throws TransportExceptionInterface
     */
    public function sendNewPasswordEmail(User $user, string $password)
    {
        $email = (new TemplatedEmail())
            ->from('info@unblockme.com')
            ->to($user->getUserIdentifier())
            ->subject('password changed successfully!')
            ->htmlTemplate('mailer/new_password.html.twig')

            ->context([
                'username' => $user->getUserIdentifier(),
                'password' => $password,
            ]);

        $this->mailer->send($email);
    }
}