<?php

namespace CB\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use CB\FairBundle\Entity\Time;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="fair_user")
 * @ORM\Entity(repositoryClass="CB\UserBundle\Entity\UserRepository")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="\CB\UserBundle\Entity\Family", inversedBy="spouses", cascade={"persist"})
     */
    private $family;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="\CB\FairBundle\Entity\Time", inversedBy="workers", cascade={"persist"})
     * @ORM\JoinTable(name="users_times")
     * @Assert\Valid
     */
    private $times;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;


    public function __construct($name)
    {
        $this->name = $name;
        $this->times = new ArrayCollection();
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
     * Set familyName
     *
     * @param Family $familyName
     * @return User
     */
    public function setFamily(Family $familyName)
    {
        $this->family = $familyName;
    
        return $this;
    }

    /**
     * Get familyName
     *
     * @return Family
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @return ArrayCollection
     *
     * @ORM\OrderBy({"booth" = "ASC", "time = "ASC"})
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * Adds the time to the worker.
     *
     * @param Time $time
     * @return $this
     */
    public function addTime(Time $time)
    {
        // If the user hasn't already signed up for this time
        if (!$this->times->contains($time)) {
            // Ad the time to the worker
            $this->times[] = $time;
        }

        return $this;
    }

    /**
     * Removes the time from the worker's list of booths
     *
     * @param Time $time
     * @return $this
     */
    public function removeTime(Time $time)
    {
        // If the user is signed up for the time:
        if ($this->times->contains($time)) {
            // Remove the time from the worker
            $this->times->removeElement($time);
        }

        return $this;
    }


    /**
     * Returns the number of hours signed up
     *
     * @return int
     */
    public function getNumOfHours()
    {
        /** @var \CB\FairBundle\Entity\Time $time */
        $count = 0;
        foreach ($this->times as $time) {
            $count += $time->getDuration();
        }
        return $count;
    }

    public function getNumOfHoursByBoothId($id)
    {
        /** @var \CB\FairBundle\Entity\Time $time */
        $count = 0;
        foreach ($this->times as $time) {
            if ($time->getBooth()->getId() == $id) {
                $count += $time->getDuration();
            }
        }
        return $count;
    }

    public function getNumOfHoursByBooth()
    {
        /** @var \CB\FairBundle\Entity\Time $time */
        $booths = array();
        foreach ($this->times as $time) {
            if (isset($booths[$time->getBooth()->getId()])) {
                $booths[$time->getBooth()->getId()] += $time->getDuration();
            } else {
                $booths[$time->getBooth()->getId()] = $time->getDuration();
            }
        }
        return $booths;
    }

    public function getTimestamps()
    {
        $timestamps = array();
        /** @var \CB\FairBundle\Entity\Time $time */
        foreach ($this->times as $time) {
            $timestamps[$time->getId()] = $time->getTimestamp();
        }

        return $timestamps;
    }

    public function getTimesArray()
    {
        $data = array();
        /** @var \CB\FairBundle\Entity\Time $time */
        foreach ($this->times as $time) {
            /** @var \CB\FairBundle\Entity\Booth $booth */
            $booth = $time->getBooth();
            $data[$booth->getName()][] = array(
                'time' => $time->getTime()->format('l ').$time,
                'location' => $booth->getLocation(),
            );
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
}
