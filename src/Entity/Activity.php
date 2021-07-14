<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="activity")
 * @ORM\Entity
 */
class Activity
{
    /**
     * @var string
     *
     * @ORM\Column(name="blocker", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $blocker;

    /**
     * @var string
     *
     * @ORM\Column(name="blockee", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $blockee;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

    /**
     * @return string
     */
    public function getBlocker(): string
    {
        return $this->blocker;
    }

    /**
     * @param string $blocker
     */
    public function setBlocker(string $blocker): Activity
    {
        $this->blocker = $blocker;
        return $this;

    }

    /**
     * @return string
     */
    public function getBlockee(): string
    {
        return $this->blockee;
    }

    /**
     * @param string $blockee
     */
    public function setBlockee(string $blockee): Activity
    {
        $this->blockee = $blockee;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int|string
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int|string $status): Activity
    {
        $this->status = $status;
        return $this;
    }

}