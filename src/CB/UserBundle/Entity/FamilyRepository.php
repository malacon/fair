<?php

namespace CB\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * FamilyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FamilyRepository extends EntityRepository
{
    /**
     * @param $username
     * @return array
     */
    public function findByUsernameChunk($username)
    {
        $username = '%'.$username.'%';
        $timestamp = new \DateTime('now');
        $timestamp = $timestamp->format('Y-m-d h:m:s');
        return $this->createQueryBuilder('u')
            ->select('u.username, u.roles')
            ->andWhere('u.username LIKE :username')
            ->andWhere('u.timeToLogin <= :time')
            ->setParameter('username', $username)
            ->setParameter('time', $timestamp)
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function isUser($username)
    {
        $result = $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result?true:false;
    }

    public function findAllUsersNotAdmins()
    {
        return $this->createQueryBuilder('f')
            ->where('f.roles NOT LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();
    }
}
