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
 * Family
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CB\UserBundle\Entity\FamilyRepository")
 */
class Family extends BaseUser
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="eldest", type="array")
     */
    private $eldest;

    /**
     * @var User
     *
     * @ORM\OneToMany(targetEntity="CB\UserBundle\Entity\User", mappedBy="family", cascade={"persist"})
     */
    private $spouses;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(name="item")
     * @ORM\OneToMany(targetEntity="\CB\FairBundle\Entity\AuctionItem", mappedBy="user", cascade={"persist"})
     * @Assert\Valid
     */
    private $saleItems;

    /**
     * @var \CB\FairBundle\Entity\BakedItem
     *
     * @ORM\Column(name="bakedItem")
     */
    private $bakedItem;

    /**
     * @var bool
     *
     * @ORM\Column(name="passedRules", type="boolean")
     */
    private $isPassedRules = false;


    public function __construct()
    {
        parent::__construct();
        $this->saleItems = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Family
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set eldest
     *
     * @param array $eldest
     * @return Family
     */
    public function setEldest($eldest)
    {
        $this->eldest = $eldest;
    
        return $this;
    }

    /**
     * Get eldest
     *
     * @return array 
     */
    public function getEldest()
    {
        return $this->eldest;
    }

    /**
     * Set spouses
     *
     * @param User $spouses
     * @return Family
     */
    public function setSpouses($spouses)
    {
        $this->spouses = $spouses;
    
        return $this;
    }

    /**
     * Get spouses
     *
     * @return User
     */
    public function getSpouses()
    {
        return $this->spouses;
    }

    /**
     * Set items
     *
     * @param \stdClass $items
     * @return Family
     */
    public function setSaleItems($items)
    {
        $this->saleItems = $items;
    
        return $this;
    }

    /**
     * Get items
     *
     * @return \stdClass 
     */
    public function getSaleItems()
    {
        return $this->saleItems;
    }

    public function getAuctionItemNames()
    {
        $names = array();
        /** @var \CB\FairBundle\Entity\AuctionItem $item */
        foreach ($this->saleItems as $item) {
            $names[] = $item->__toString();
        }
        return $names;
    }

    /**
     * Adds an auction item if it doesn't already exist
     *
     * @param AuctionItem $saleItem
     * @return $this
     */
    public function addSaleItem(AuctionItem $saleItem)
    {
        $saleItem->setFamily($this);
        $this->saleItems->add($saleItem);

        return $this;
    }

    /**
     * Removes the auction item if it exists
     *
     * @param \CB\FairBundle\Entity\AuctionItem $saleItem
     * @return $this
     */
    public function removeSaleItem(AuctionItem $saleItem)
    {
        if ($this->saleItems->contains($saleItem)) {
            $this->saleItems->removeElement($saleItem);
        }

        return $this;
    }

    /**
     * Sets bakedItem
     *
     * @param \CB\FairBundle\Entity\BakedItem $bakedItem
     * @return Family
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
     * Get bakedItems
     *
     * @return BakedItem
     */
    public function getBakedItem()
    {
        return $this->bakedItem;
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
     * Returns whether or not the family has a baked item
     *
     * @return bool
     */
    public function hasBakedItem()
    {
        return (bool)($this->bakedItem == null? 0 : 1);
    }

    /**
     * Returns the number of auction items
     *
     * @return int
     */
    public function getNumOfAuctionItems()
    {
        return $this->saleItems->count();
    }

    /**
     * Returns the number of hours signed up
     *
     * @return int
     */
    public function getNumOfHours()
    {
        $hours = 0;
        /** @var \CB\UserBundle\Entity\User $spouse */
        foreach ($this->spouses as $spouse) {
            $hours += $spouse->getTimes()->count();
        }
        return $hours;
    }

    public function getTimestamps()
    {
        $timestamps = array();
        /** @var \CB\UserBundle\Entity\User $spouse */
        foreach ($this->spouses as $spouse) {
            $timestamps[$spouse->getName()] = $spouse->getTimestamps();
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
        /** @var \CB\UserBundle\Entity\User $spouse */
        foreach ($this->spouses as $spouse) {
            $data[$spouse->getName()] = $spouse->getTimesArray();
        }
        return $data;
    }

    public function getAuctionItemsArray()
    {
        $data = array();
        /** @var \CB\FairBundle\Entity\AuctionItem $item */
        foreach ($this->saleItems as $item) {
            $data[] = (string)$item;
        }
        return $data;
    }
}
