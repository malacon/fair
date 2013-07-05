<?php

namespace CB\FairBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use CB\FairBundle\Entity\Booth;
use Gedmo\Mapping\Annotation as Gedmo;
use CB\UserBundle\Entity\User;

/**
 * Time
 *
 * @ORM\Table(name="fair_time")
 * @ORM\Entity(repositoryClass="CB\FairBundle\Entity\TimeRepository")
 */
class Time
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
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Booth", inversedBy="time")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $booth;

    /**
     * @var integer
     *
     * @ORM\ManyToMany(targetEntity="CB\UserBundle\Entity\User", mappedBy="times")
     * @ORM\JoinTable(name="fair_times_users")
     */
    private $users;

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
        $this->users = new ArrayCollection();
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
     * Set time
     *
     * @param \DateTime $time
     * @return Time
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Add user
     *
     * @param User $user
     * @return Time
     */
    public function addUser(User $user)
    {
        $user->addBoothTime($this);
        $this->users[] = $user;
    
        return $this;
    }

    /**
     * Get users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return int
     */
    public function getBooth()
    {
        return $this->booth;
    }

    /**
     * @param int $booth
     */
    public function setBooth(Booth $booth)
    {
        $this->booth = $booth;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->time->format('D\, M jS \a\t g A');
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Time
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
     * @return Time
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