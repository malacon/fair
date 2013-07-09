<?php

namespace CB\FairBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use CB\FairBundle\Entity\Booth;
use Gedmo\Mapping\Annotation as Gedmo;
use CB\UserBundle\Entity\User;

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
    private $workers;

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
        $this->workers = new ArrayCollection();
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
     * Gets the number of workers at a given time for the booth
     *
     * @return int
     */
    public function getNumOfWorkers()
    {
        return $this->workers->count();
    }

    /**
     * Tells if time is filled with workers
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
    public function getWorkers()
    {
        return $this->workers;
    }

    /**
     * Adds worker to the booth time
     *
     * @param User $worker
     * @return Bool  If the sign-up is successful it returns true
     */
    public function addWorker(User $worker)
    {
        // If the worker is signed up
         if (!$this->workers->contains($worker) && !$this->isUserAlreadySignedUpAtThisTime($worker) && $this->isAvailable()) {
            // Add the time to the worker
            $worker->addTime($this);
            // Add the worker to the time
            $this->workers[] = $worker;
            return true;
        }

        return false;
    }

    /**
     * Checks to see if the user is already signed up for the time
     *
     * @param User $worker
     * @return bool
     */
    public function isUserAlreadySignedUpAtThisTime(User $worker)
    {
        /** @var \CB\FairBundle\Entity\Time $time */
        foreach ($worker->getTimes() as $time) {
            // If the requested time is already on the user's schedule return true
            if ($this->getTime()->diff($time->getTime())->h == 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks to see if there are open spots during the time
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getNumOfWorkers() < $this->booth->getWorkerLimit();
    }


    /**
     * Removes a worker from the booth time
     *  OWNER
     *
     * @param User $worker
     * @return Bool
     */
    public function removeWorker(User $worker)
    {
        // If the worker isn't already signed up
        if ($this->workers->contains($worker)) {
            // Remove the time from the worker
            $worker->removeTime($this);
            // Remove the worker from the time
            $this->workers->removeElement($worker);
            return true;
        }

        return false;
    }

    /**
     * @param User $worker
     * @return bool
     */
    public function hasWorker(User $worker)
    {
        return $this->getWorkers()->contains($worker);
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
//        $nextHour = clone $this->time;
//        $nextHour->add(new \DateInterval('P01H'));
//        $this->time->diff($this->time->add(new \DateInterval('P01H')))->format()
        return (string)$this->time->format('D\, M jS \a\t g A');//.' - '.(string)$nextHour->format('g A');
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