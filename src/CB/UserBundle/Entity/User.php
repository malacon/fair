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
     * @return ArrayCollection
     */
    public function getAuctionItems()
    {
        return $this->auctionItems;
    }

    public function getAuctionItemNames()
    {
        $names = array();
        /** @var \CB\FairBundle\Entity\AuctionItem $item */
        foreach ($this->auctionItems as $item) {
            $names[] = $item->getDescription();
        }
        return $names;
    }

    /**
     * Adds an auction item if it doesn't already exist
     *
     * @param AuctionItem $auctionItem
     * @return $this
     */
    public function addAuctionItem(AuctionItem $auctionItem)
    {
        $auctionItem->setUser($this);
        $this->auctionItems->add($auctionItem);

        return $this;
    }

    /**
     * Removes the auction item if it exists
     *
     * @param AuctionItem $auctionItem
     * @return $this
     */
    public function removeAuctionItem(AuctionItem $auctionItem)
    {
        if ($this->auctionItems->contains($auctionItem)) {
            $this->auctionItems->removeElement($auctionItem);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBakedItem()
    {
        return $this->bakedItem;
    }

    /**
     * Adds a baked item to the user
     *
     * @param BakedItem $bakedItem
     * @return $this
     */
    public function setBakedItem(BakedItem $bakedItem)
    {
        // if baked item already set remove it from the worker
        if ($this->bakedItem != null) {
            $this->bakedItem->removeWorker($this);
        }
        $this->bakedItem = $bakedItem;

        return $this;
    }

    /**
     * Adds a baked item to the user
     *
     * @return $this
     */
    public function removeBakedItem()
    {
        $this->bakedItem->removeWorker($this);
        $this->bakedItem = null;

        return $this;
    }

    public function isBaking()
    {
        return (bool)$this->bakedItem;
    }

    /**
     * @param BakedItem $item
     * @return bool
     */
    public function isBakingItem(BakedItem $item)
    {
        return $item == $this->bakedItem;
    }

    /**
     * Returns if the rules are passed for registration
     *
     * @return bool
     */
    public function getIsPassedRules()
    {
        return $this->isPassedRules;
    }

    /**
     * Sets the user as having passed the rules or not
     *
     * @param array $rules
     * @return $this
     */
    public function setIsPassedRules($rules)
    {
        /** @var \CB\FairBundle\Entity\Rule $rule */
        $isPassed = false;
        foreach ($rules as $rule) {
            // Checks to see if the rules pass for the user
            $isPassed = $rule->isPassed($this);
            if ($isPassed) {
                break;
            }
        }
        $this->isPassedRules = $isPassed;
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

    /**
     * Returns the number of baked items
     *
     * @return int
     */
    public function hasBakedItem()
    {
        return ($this->bakedItem == null? 0 : 1);
    }

    /**
     * Returns the number of auction items
     *
     * @return int
     */
    public function getNumOfAuctionItems()
    {
        return $this->auctionItems->count();
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

    public function getStatus()
    {
        return array(
            'auctionItems' => $this->getAuctionItemsArray(),
            'boothTimes' => $this->getTimesArray(),
            'bakedItem' => (string)$this->getBakedItem(),
        );
    }

    public function getTimesArray()
    {
        $data = array();
        /** @var \CB\FairBundle\Entity\Time $time */
        foreach ($this->times as $time) {
            $data[$time->getBooth()->getName()][] = array(
                'time' => $time->getTime()->format('l ').$time,
                'location' => $time->getBooth()->getLocation(),
            );
        }
        return $data;
    }

    public function getAuctionItemsArray()
    {
        $data = array();
        /** @var \CB\FairBundle\Entity\AuctionItem $item */
        foreach ($this->auctionItems as $item) {
            $data[] = (string)$item;
        }
        return $data;
    }

    /**
     * @return Array
     */
    public function getSpouses()
    {
        return $this->spouses;
    }

    /**
     * @param Array $spouses
     */
    public function setSpouses($spouses)
    {
        $this->spouses = $spouses;

        return $this;
    }
}
