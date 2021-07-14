<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\String\UnicodeString;
use App\Entity\Activity;
use App\Entity\LicensePlate;
use App\Form\BlockeeType;
use App\Form\BlockerType;
use App\Repository\LicensePlateRepository;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/report', name: 'report', methods: ['GET', 'POST'])]
    public function report(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('report/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/mycars', name: 'mycars', methods: ['GET'])]
    public function showMyCars(): Response
    {
        return $this->redirectToRoute('license_plate_index');
    }

    #[Route('/blockee', name: 'blockee', methods: ['GET', 'POST'])]
    public function blcokedMe(Request $request, LicensePlateRepository $licensePlate, MailerService $mailer): Response
    {
        $activity = new Activity();
        $blockedCars = $licensePlate->findBy(['user'=>$this->getUser()]);
        if (count($blockedCars) == 0) {
            $this->addFlash (
                'warning',
                "You must enter at least one vehicle for this functionality !"
            );

            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(BlockeeType::class, $activity);

        if(count($blockedCars) == 1)
        {
            $activity->setBlockee($blockedCars[0]);
            $form->add('blockee', TextType::class, ['disabled'=>true]);
        }
        else
        {
            $form->add('blockee', EntityType::class, [
                'class' => LicensePlate::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.user = :val')
                        ->setParameter('val', $this->getUser());
                },
                'choice_label' => 'license_plate'
            ]);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $activity->setBlockee((new UnicodeString($activity->getBlockee()))->camel()->upper());
            $activity->setBlocker((new UnicodeString($activity->getBlocker()))->camel()->upper());
            $entityManager->persist($activity);
            $entityManager->flush();
            $new = $licensePlate->findOneBy(['license_plate'=>$activity->getBlocker()]);
            if($new)
            {
                $blockee = $licensePlate->findOneBy(['license_plate'=>$activity->getBlockee()]);
                $mailer->sendBlockerReport($blockee->getUser(),$new->getUser(), $blockee->getLicensePlate());
                $message = "The owner of the car ".$activity->getBlocker()." has been notified !";
                $this->addFlash(
                    'success',
                    $message
                );
            }
            else
            {
                $licensePlate = new LicensePlate();
                $entityManager = $this->getDoctrine()->getManager();
                $licensePlate->setLicensePlate($activity->getBlocker());
                $entityManager->persist($licensePlate);
                $entityManager->flush();
                $message = "The owner of the car ".$activity->getBlocker()." is not registered for moment !";
                $this->addFlash(
                    'warning',
                    $message
                );
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('blockee/new.html.twig', [
            'activity' => $activity,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/blocker', name: 'blocker', methods: ['GET', 'POST'])]
    public function blockedSomeone(Request $request, LicensePlateRepository $licensePlate, MailerService $mailer): Response
    {
        $activity = new Activity();
        $blockedCars = $licensePlate->findBy(['user'=>$this->getUser()]);
        if (count($blockedCars) == 0) {
            $this->addFlash (
                'warning',
                "You must enter at least one vehicle for this functionality !"
            );

            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(BlockerType::class, $activity);

        if(count($blockedCars) == 1)
        {
            $activity->setBlocker($blockedCars[0]);
            $form->add('blocker', TextType::class, ['disabled'=>true]);
        }
        else
        {
            $form->add('blocker', EntityType::class, [
                'class' => LicensePlate::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.user = :val')
                        ->setParameter('val', $this->getUser());
                },
                'choice_label' => 'license_plate'
            ]);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $activity->setBlockee((new UnicodeString($activity->getBlockee()))->camel()->upper());
            $activity->setBlocker((new UnicodeString($activity->getBlocker()))->camel()->upper());
            $entityManager->persist($activity);
            $entityManager->flush();
            $new = $licensePlate->findOneBy(['license_plate'=>$activity->getBlockee()]);
            if($new)
            {
                $blocker = $licensePlate->findOneBy(['license_plate'=>$activity->getBlocker()]);
                $mailer->sendBlockeeReport($blocker->getUser(),$new->getUser(), $blocker->getLicensePlate());
                $message = "The owner of the car ".$activity->getBlockee()." has been emailed!";
                $this->addFlash(
                    'success',
                    $message
                );
            }
            else
            {
                $licensePlate = new LicensePlate();
                $entityManager = $this->getDoctrine()->getManager();
                $licensePlate->setLicensePlate($activity->getBlockee());
                $entityManager->persist($licensePlate);
                $entityManager->flush();
                $message = "The owner of the car ".$activity->getBlockee()." is not registered for moment !";
                $this->addFlash(
                    'warning',
                    $message
                );
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('blocker/new.html.twig', [
            'activity' => $activity,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/password', name: 'password_new', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, MailerService $mailer) : Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->add('old_password', PasswordType::class, array('mapped' => false));

        $form->add('new_password', PasswordType::class, array('mapped' => false));

        $form->add('confirm_new_password', PasswordType::class, array('mapped' => false));

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $oldPassword = $form->get('old_password')->getData();

            $newPassword = $form->get('new_password')->getData();

            $confirmNewPassword = $form->get('confirm_new_password')->getData();

            $isValid = true;

            if($user->getEmail() != $this->getUser()->getUserIdentifier()) {
                $this->addFlash(
                    'danger',
                    "This is not the email associated with the account. Try again"
                );
                $isValid = false;
            }
            else if(!$passwordHasher->isPasswordValid($this->getUser(), $oldPassword))
            {
                $this->addFlash(
                    'danger',
                    "The old password is incorrect. Try again"
                );
                $isValid = false;
            }

            if (strlen($newPassword) < 8)  {
                $this->addFlash(
                    'danger',
                    "The new password is too short. Try again"
                );
                $isValid = false;
            } else if (strlen($newPassword) > 25) {
                $this->addFlash(
                    'danger',
                    "The new password is too large. Try again"
                );
                $isValid = false;
            } else if($newPassword != $confirmNewPassword) {
                $this->addFlash(
                    'danger',
                    "The new password does not match. Try again"
                );
                $isValid = false;
            }

            if(!$isValid) {
                return $this->redirectToRoute('password_new');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $this->getUser()->setPassword($passwordHasher->hashPassword($this->getUser(), $newPassword));

            $entityManager->persist($this->getUser());

            $entityManager->flush();

            $this->addFlash (
                'success',
                "The password has been successfully changed!"
            );

            $mailer->sendNewPasswordEmail($user, $newPassword);

            return $this->redirectToRoute('home');
        }

        return $this->render('password/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}