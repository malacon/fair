<?php

namespace CB\FairBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BakedItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BakedItemRepository extends EntityRepository
{
    public function getBakedItemsThatAreFilledAsArray()
    {
        return $this->createQueryBuilder('b')
            ->select('b.id, b.description, b.quantity')
            ->where('b.available = false')
            ->getQuery()
            ->getArrayResult();
    }
}
