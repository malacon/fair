<?php

namespace CB\FairBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserTime
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CB\FairBundle\Entity\UserTimeRepository")
 */
class UserTime
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
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="timeId", type="integer")
     */
    private $timeId;

    /**
     * @var integer
     *      Will be the array key value in the user key
     *
     * @ORM\Column(name="spouse", type="integer")
     */
    private $spouse;


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
     * Set userId
     *
     * @param integer $userId
     * @return UserTime
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set timeId
     *
     * @param integer $timeId
     * @return UserTime
     */
    public function setTimeId($timeId)
    {
        $this->timeId = $timeId;
    
        return $this;
    }

    /**
     * Get timeId
     *
     * @return integer 
     */
    public function getTimeId()
    {
        return $this->timeId;
    }

    /**
     * Set spouse
     *
     * @param integer $spouse
     * @return UserTime
     */
    public function setSpouse($spouse)
    {
        $this->spouse = $spouse;
    
        return $this;
    }

    /**
     * Get spouse
     *
     * @return integer 
     */
    public function getSpouse()
    {
        return $this->spouse;
    }
}
