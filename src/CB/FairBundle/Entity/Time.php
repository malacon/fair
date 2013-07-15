<?php

namespace CB\FairBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use CB\FairBundle\Entity\Booth;
use Gedmo\Mapping\Annotation as Gedmo;
use CB\UserBundle\Entity\User;
use CB\UserBundle\Entity\Family;

/**
 * Time
 *
 * @ORM\Table(name="fair_time")
 * @ORM\Entity(repositoryClass="CB\FairBundle\Entity\TimeRepository")
 */
class Time
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var \CB\FairBundle\Entity\Booth
     *
     * @ORM\ManyToOne(targetEntity="Booth", inversedBy="times", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $booth;

    /**
     * @ORM\ManyToMany(targetEntity="CB\UserBundle\Entity\User", mappedBy="times", cascade={"persist"})
     */
    private $spouses;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    public function __construct()
    {
        $this->spouses = new ArrayCollection();
        $this->created = new \DateTime('NOW');
        $this->updated = new \DateTime('NOW');
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Time
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Gets the number of spouses at a given time for the booth
     *
     * @return int
     */
    public function getNumOfSpouses()
    {
        return $this->spouses->count();
    }

    /**
     * Tells if time is filled with spouses
     *
     * @return bool
     */
    public function isFilled()
    {
        return !$this->isAvailable();
    }

    /**
     * Get users
     *
     * @return ArrayCollection
     */
    public function getSpouses()
    {
        return $this->spouses;
    }

    /**
     * Adds spouse to the booth time
     *
     * @param User $spouse
     * @return Bool  If the sign-up is successful it returns true
     */
    public function addSpouse(User $spouse)
    {
        // If the worker is signed up
         if (!$this->spouses->contains($spouse) && !$this->isSpouseAlreadySignedUpAtThisTime($spouse) && $this->isAvailable()) {
            // Add the time to the worker
            $spouse->addTime($this);
            // Add the worker to the time
            $this->spouses[] = $spouse;
            return true;
        }

        return false;
    }

    /**
     * Checks to see if the spouse is already signed up for the time
     *
     * @param User $spouse
     * @return bool
     */
    public function isSpouseAlreadySignedUpAtThisTime(User $spouse)
    {
        /** @var \CB\FairBundle\Entity\Time $time */
        foreach ($spouse->getTimes() as $time) {
            // If the requested time is already on the user's schedule return true
            if ($this->getTime()->diff($time->getTime())->h == 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param User $spouse current spouse
     *
     * To see if partner is working
     */
    public function isOtherSpouseSignedUpAtThisTime(User $spouse)
    {
        /** @var \CB\UserBundle\Entity\User $otherSpouses */
        foreach ($spouse->getFamily()->getSpouses() as $otherSpouses) {
            if ($otherSpouses != $spouse && $this->hasSpouse($otherSpouses)) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param \CB\UserBundle\Entity\Family $family
     *
     * true false false = false
     * true true false = false
     * true true true = true
     */
    public function areAllSpousesWorkingAtThisTime(Family $family)
    {
        foreach ($family->getSpouses() as $spouse) {
             if (!$this->isSpouseAlreadySignedUpAtThisTime($spouse)) {
                 return false;
             }
        }
        return true;
    }

    /**
     * Checks to see if there are open spots during the time
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getNumOfSpouses() < $this->booth->getWorkerLimit();
    }


    /**
     * Removes a worker from the booth time
     *  OWNER
     *
     * @param User $spouse
     * @return Bool
     */
    public function removeSpouse(User $spouse)
    {
        // If the worker isn't already signed up
        if ($this->spouses->contains($spouse)) {
            // Remove the time from the worker
            $spouse->removeTime($this);
            // Remove the worker from the time
            $this->spouses->removeElement($spouse);
            return true;
        }

        return false;
    }

    /**
     * @param User $worker
     * @return bool
     */
    public function hasSpouse(User $spouse)
    {
        return $this->getSpouses()->contains($spouse);
    }

    /**
     * @return int
     */
    public function getBooth()
    {
        return $this->booth;
    }

    /**
     * @param int $booth
     */
    public function setBooth(Booth $booth)
    {
        $this->booth = $booth;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $nextHour = new \DateTime($this->getTime()->format('Y-m-d H:i:s'));
        $nextHour->add(new \DateInterval('PT01H'));
        return $this->time->format('g A').' - '.$nextHour->format('g A');
    }


    public function getTimestamp()
    {
        return $this->getTime()->getTimestamp();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Time
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Time
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

}