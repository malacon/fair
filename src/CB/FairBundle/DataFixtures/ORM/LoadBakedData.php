<?php
namespace CB\FairBundle\DataFixtures\ORM;

use CB\FairBundle\Entity\BakedItem;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

class LoadBakedData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = $this->getReference('user-user');

        $item1 = new BakedItem();
        $item1->setDescription('cookies');
        $item1->setQuantity(12);
        $item1->setUser($user);

        $manager->persist($item1);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 20;
    }
}

