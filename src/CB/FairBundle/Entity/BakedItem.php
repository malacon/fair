<?php

namespace CB\FairBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use CB\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BakedITem
 *
 * @ORM\Table(name="fair_baked_items")
 * @ORM\Entity(repositoryClass="CB\FairBundle\Entity\BakedItemRepository")
 */
class BakedItem
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
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min = "3")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\NotBlank()
     */
    private $quantity;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CB\UserBundle\Entity\Family", mappedBy="bakedItem", cascade={"persist"})
     */
    private $workers;

    /**
     * @var bool
     *
     * @ORM\Column(name="available", type="boolean")
     */
    private $available;

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
        $this->created = new \DateTime('NOW');
        $this->updated = new \DateTime('NOW');
        $this->workers = new ArrayCollection();
        $this->available = true;
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
     * Set description
     *
     * @param string $description
     * @return BakedITem
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
     * Set quantity
     *
     * @param string $quantity
     * @return BakedITem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->available = $this->quantity>0?true:false;
    
        return $this;
    }

    /**
     * Get quantity
     *
     * @return string 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Get family
     *
     * @return integer 
     */
    public function getWorkers()
    {
        return $this->workers;
    }

    /**
     * Adds worker to the baked item
     *
     * @param Family $worker
     * @return bool
     */
    public function addWorker(Family $worker)
    {
        // If the worker is signed up
        if (!$this->workers->contains($worker) && $this->isItemAvailable()) {
            // Add the time to the worker
            $worker->setBakedItem($this);
            // Add the worker to the time
            $this->workers[] = $worker;
            $this->available = $this->isItemAvailable();
            return true;
        }

        return false;
    }

    /**
     * Removes a worker from the baked item
     *  OWNER
     *
     * @param Family $worker
     * @return Bool
     */
    public function removeWorker(Family $worker)
    {
        // If the worker isn't already signed up
        if ($this->workers->contains($worker)) {
            // Remove the time from the worker
            // Remove the worker from the time
            $this->workers->removeElement($worker);
            $this->available = $this->isItemAvailable();
            return true;
        }

        return false;
    }

    public function getNumberOfFamiles()
    {
        return $this->workers->count();
    }

    /**
     * @param Family $worker
     */
    public function isFamilyAlreadyBringingABakedItem($worker)
    {
        return $worker->isBaking();
    }

    public function isItemAvailable()
    {
        return $this->getNumberOfFamiles() < $this->quantity;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BakedItem
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
     * @return BakedItem
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
        return $this->description;
    }
}