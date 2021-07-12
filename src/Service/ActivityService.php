<?php


namespace App\Service;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;

class ActivityService
{
    /**
     * @var ActivityRepository
     */
    protected $activityRepo;
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->activityRepo = $em->getRepository(Activity::class);
    }

    /**
     * @param string $licensePlate
     * @return string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function iHaveBlockedSomeone(string $licensePlate): ?string
    {
        $blocker = $this->activityRepo->findByBlocker($licensePlate);

        if ($blocker instanceof Activity){
            return $blocker->getBlockee();
        }
        return '';
    }

    /**
     * @param string $licensePlate
     * @return string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function iWasBlocked(string $licensePlate): ?string
    {
        $blocker = $this->activityRepo->findByBlockee($licensePlate);

        if ($blocker instanceof Activity){
            return $blocker->getBlocker();
        }
        return '';
    }
}