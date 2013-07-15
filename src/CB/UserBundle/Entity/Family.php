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
     * @var \stdClass
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
     * @param \stdClass $spouses
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
     * @return \stdClass 
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

    /**
     * Set bakedItems
     *
     * @param \stdClass $bakedItems
     * @return Family
     */
    public function setBakedItem($bakedItems)
    {
        $this->bakedItem = $bakedItems;
    
        return $this;
    }

    /**
     * Get bakedItems
     *
     * @return \stdClass 
     */
    public function getBakedItem()
    {
        return $this->bakedItem;
    }
}
