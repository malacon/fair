<?php

namespace CB\FairBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints\DateTime;
use CB\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AuctionItem
 *
 * @ORM\Table(name="fair_auction_item")
 * @ORM\Entity(repositoryClass="CB\FairBundle\Entity\AuctionItemRepository")
 */
class AuctionItem
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
     * @ORM\Column(name="type", type="string")
     * @Assert\Choice(choices = {"craft", "auction"}, message = "Choose a valid item type")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="\CB\UserBundle\Entity\User", inversedBy="auctionItems", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    public function __construct()
    {
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
     * Set user
     *
     * @param integer $user
     * @return AuctionItem
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    public function addUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return AuctionItem
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
     * @return AuctionItem
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

    public function __toString()
    {
        if ($this->type == 'craft') {
            return '$25 craft item';
        } else {
            return '$100 auction item';
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}