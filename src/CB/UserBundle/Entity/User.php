<?php

namespace CB\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use CB\FairBundle\Entity\Time;
use CB\FairBundle\Entity\AuctionItem;
use CB\FairBundle\Entity\BakedItem;
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
     * @ORM\OneToMany(targetEntity="\CB\FairBundle\Entity\AuctionItem", mappedBy="user", cascade={"persist"})
     * @Assert\Valid
     */
    private $auctionItems;

    /**
     * @var \CB\FairBundle\Entity\BakedItem
     *
     * @ORM\ManyToOne(targetEntity="\CB\FairBundle\Entity\BakedItem", inversedBy="workers", cascade={"persist"})
     * @ORM\JoinColumn(name="baked_item_id", referencedColumnName="id")
     */
    private $bakedItem;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="\CB\FairBundle\Entity\Time", inversedBy="workers", cascade={"persist"})
     * @ORM\JoinTable(name="users_times")
     * @Assert\Valid
     */
    private $times;

    public function __construct()
    {
        parent::__construct();
        $this->times = new ArrayCollection();
        $this->auctionItems = new ArrayCollection();
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
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
    public function getFamilyName()
    {
        return $this->family;
    }

    /**
     * @return ArrayCollection
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
        return $this->times->count();
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
    }
}
