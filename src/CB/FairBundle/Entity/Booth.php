<?php

namespace CB\FairBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Booth
 *
 * @ORM\Table(name="fair_booth")
 * @ORM\Entity(repositoryClass="CB\FairBundle\Entity\BoothRepository")
 */
class Booth
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
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $workerLimit;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Time", mappedBy="booth", cascade={"persist"})
     * @ORM\OrderBy({"time" = "ASC"})
     */
    private $times;

    /**
     * @var /Datetime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var /Datetime
     *
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", length=255, unique=true, nullable=true)
     */
    protected $slug;

    public function __construct()
    {
        $this->times = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Booth
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
     * Set description
     *
     * @param string $description
     * @return Booth
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
     * Set location
     *
     * @param string $location
     * @return Booth
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return int
     */
    public function getWorkerLimit()
    {
        return $this->workerLimit;
    }

    public function getNumberOfWorkersWorkingAtTime(Time $time)
    {

    }

    /**
     * @param int $quantity
     */
    public function setWorkerLimit($quantity)
    {
        $this->workerLimit = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimes()
    {
        return $this->times;
    }

    public function addTime(Time $time)
    {
        if (!$this->times->contains($time)) {
            $time->setBooth($this);
            $this->times->add($time);
        }

        return $this;
    }

    public function removeTime(Time $time)
    {
        if ($this->times->contains($time)) {
            $this->times->removeElement($time);
        }
    }

    public function __toString()
    {
        return (!is_string($this->name)) ? 'Booth not set' : $this->name;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Booth
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
     * @return Booth
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
}