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
        $item1 = new BakedItem();
        $item1->setDescription('2 Dozen Cookies')
            ->setQuantity(12);

        $item2 = new BakedItem();
        $item2->setDescription('2 Dozen Brownies')
            ->setQuantity(1);

        $item3 = new BakedItem();
        $item3->setDescription('42 Dozen Pies')
            ->setQuantity(0);

        $manager->persist($item1);
        $manager->persist($item2);
        $manager->persist($item3);
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

