<?php

namespace CB\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use CB\FairBundle\Entity\Time;
use CB\FairBundle\Entity\AuctionItem;
use CB\FairBundle\Entity\BakedItem;

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
     * @ORM\OneToMany(targetEntity="CB\FairBundle\Entity\AuctionItem", mappedBy="user")
     */
    private $auctionItems;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CB\FairBundle\Entity\BakedItem", mappedBy="user")
     */
    private $bakedItems;

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
     * @return int
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * @param int $times
     */
    public function addTime(Time $time)
    {
        $this->times[] = $time;
    }

    /**
     * @return int
     */
    public function getAuctionItems()
    {
        return $this->auctionItems;
    }

    /**
     * @param int $auctionItems
     */
    public function addAuctionItems(AuctionItem $auctionItem)
    {
        $this->auctionItems[] = $auctionItem;
    }

    /**
     * @return int
     */
    public function getBakedItems()
    {
        return $this->bakedItems;
    }

    /**
     * @param int $bakedItem
     */
    public function addBakedItems(BakedItem $bakedItem)
    {
        $this->bakedItems[] = $bakedItem;
    }
}
