<?php

namespace CB\FairBundle\Entity;

use CB\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Rule
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CB\FairBundle\Entity\RuleRepository")
 */
class Rule
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
     * @var integer
     *
     * @ORM\Column(name="numberOfTimes", type="integer")
     */
    private $numberOfTimes = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOfBakedItems", type="integer")
     */
    private $numberOfBakedItems = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOfAuctionItems", type="integer")
     */
    private $numberOfAuctionItems = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;


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
     * Set numberOfTimes
     *
     * @param integer $numberOfTimes
     * @return Rule
     */
    public function setNumberOfTimes($numberOfTimes)
    {
        $this->numberOfTimes = $numberOfTimes;
    
        return $this;
    }

    /**
     * Get numberOfTimes
     *
     * @return integer 
     */
    public function getNumberOfTimes()
    {
        return $this->numberOfTimes;
    }

    /**
     * Set numberOfBakedItems
     *
     * @param integer $numberOfBakedItems
     * @return Rule
     */
    public function setNumberOfBakedItems($numberOfBakedItems)
    {
        $this->numberOfBakedItems = $numberOfBakedItems;
    
        return $this;
    }

    /**
     * Get numberOfBakedItems
     *
     * @return integer 
     */
    public function getNumberOfBakedItems()
    {
        return $this->numberOfBakedItems;
    }

    /**
     * Set numberOfAuctionItems
     *
     * @param integer $numberOfAuctionItems
     * @return Rule
     */
    public function setNumberOfAuctionItems($numberOfAuctionItems)
    {
        $this->numberOfAuctionItems = $numberOfAuctionItems;
    
        return $this;
    }

    /**
     * Get numberOfAuctionItems
     *
     * @return integer 
     */
    public function getNumberOfAuctionItems()
    {
        return $this->numberOfAuctionItems;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Rule
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Rule
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Rule
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * This checks to see if the current rule is passed or not
     *
     * @param User $worker
     * @return bool
     */
    public function isPassed(User $worker)
    {
        return
            $this->hasEnoughAuctionItems($worker->getNumOfAuctionItems()) &&
            $this->hasEnoughBakedItems($worker->getNumOfBakedItems()) &&
            $this->hasEnoughHours($worker->getNumOfHours());
    }

    /**
     * Checks to see if the auction items are sufficient
     *
     * @param int $numberOfAuctionItems
     * @return bool
     */
    public function hasEnoughAuctionItems($numberOfAuctionItems)
    {
        return ($numberOfAuctionItems >= $this->numberOfAuctionItems);
    }

    /**
     * Checks to see if the baked items are sufficient
     *
     * @param int $numberOfBakedItems
     * @return bool
     */
    public function hasEnoughBakedItems($numberOfBakedItems)
    {
        return ($numberOfBakedItems >= $this->numberOfBakedItems);
    }

    /**
     * Checks to see if the hours are sufficient
     *
     * @param int $numberOfHours
     * @return bool
     */
    public function hasEnoughHours($numberOfHours)
    {
        return ($numberOfHours >= $this->numberOfTimes);
    }

    public function __toString()
    {
        return $this->description.': '.$this->numberOfTimes.' hours, '.$this->numberOfBakedItems.' baked items, and '.$this->numberOfAuctionItems.' auction items';
    }
}
