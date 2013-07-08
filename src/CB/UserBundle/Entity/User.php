<?php

namespace CB\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
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
class User extends BaseUser
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
     * @ORM\Column(name="familyName", type="string", length=255)
     */
    private $familyName;

    /**
     * @var array
     *
     * @ORM\Column(name="children", type="array")
     */
    private $children;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\CB\FairBundle\Entity\AuctionItem", mappedBy="user", cascade={"persist"})
     * @Assert\Valid
     */
    private $auctionItems;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\CB\FairBundle\Entity\BakedItem", mappedBy="user", cascade={"persist"})
     * @Assert\Valid
     */
    private $bakedItems;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="\CB\FairBundle\Entity\Time", inversedBy="workers", cascade={"persist"})
     * @ORM\JoinTable(name="users_times")
     * @Assert\Valid
     */
    private $times;

    /**
     * @var bool
     *
     * @ORM\Column(name="passedRules", type="boolean")
     */
    private $isPassedRules = false;

    public function __construct()
    {
        parent::__construct();
        $this->times = new ArrayCollection();
        $this->auctionItems = new ArrayCollection();
        $this->bakedItems = new ArrayCollection();
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
     * @param string $familyName
     * @return User
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    
        return $this;
    }

    /**
     * Get familyName
     *
     * @return string 
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Set children
     *
     * @param array $children
     * @return User
     */
    public function setChildren($children)
    {
        $this->children = $children;
    
        return $this;
    }

    /**
     * Get children
     *
     * @return array 
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function addChild($name, $grade)
    {
        $this->children[] = array('name' => $name, 'grade' => $grade);
    }

    /**
     * @return ArrayCollection
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
    public function getBakedItems()
    {
        return $this->bakedItems;
    }

    /**
     * Adds a baked item to the user
     *
     * @param BakedItem $bakedItem
     * @return $this
     */
    public function addBakedItem(BakedItem $bakedItem)
    {
        if (!$this->bakedItems->contains($bakedItem)) {
            $bakedItem->setUser($this);
            $this->bakedItems[] = $bakedItem;
        }

        return $this;
    }

    /**
     * Adds a baked item to the user
     *
     * @param BakedItem $bakedItem
     * @return $this
     */
    public function removeBakedItem(BakedItem $bakedItem)
    {
        if ($this->bakedItems->contains($bakedItem)) {
            $this->bakedItems->removeElement($bakedItem);
        }

        return $this;
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
    public function getNumOfBakedItems()
    {
        return $this->bakedItems->count();
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
}
