<?php

namespace App\Controller;

use App\Entity\LicensePlates;
use App\Form\BlockeeType;
use App\Form\BlockerType;
use App\Repository\ActivityRepository;
use App\Form\ActivityType;
use Doctrine\DBAL\Types\TextType;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Activity;
use App\Entity\User;

class ActivityController extends AbstractController
{
    #[Route('/activity', name: 'activity')]
    public function index(ActivityRepository $activityRepository): Response
    {
        $user =$this->getUser();

        if($user == null) {
            return $this->redirectToRoute('app_login');
        }

        $licensePlateIterator = $user->getLicensePlates();

        $activities = array();

        foreach($licensePlateIterator as $licensePlate)
        {
            $lp = $licensePlate->getLicensePlate();

            $var = $activityRepository->findByBlocker($lp);

            if($var)
            {
                foreach ($var as $res)
                    array_push($activities,$res);
            }

            $result = $activityRepository->findByBlockee($lp);

            if($result)
            {
                foreach ($result as $res)
                    array_push($activities,$res);
            }
        }

        return $this->render('activity/index.html.twig', [
            'controller_name' => 'ActivityController',
            'activityRepository' => $activityRepository->findAll(),
            'activeActivities' => $activities,
        ]);
    }


    #[Route('/delete_activity/{blocker}/{blockee}', name: 'delete_activity', methods: ['GET', 'POST'])]
    public function delete(Request $request, Activity $activity): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($activity);
        $entityManager->flush();

        return $this->redirectToRoute('activity');
    }

}