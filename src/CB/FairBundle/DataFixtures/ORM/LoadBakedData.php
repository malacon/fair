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
        $item2 = new BakedItem();
        $item3 = new BakedItem();
        $item4 = new BakedItem();
        $item5 = new BakedItem();
        $item6 = new BakedItem();
        $item7 = new BakedItem();
        $item8 = new BakedItem();
        $item9 = new BakedItem();
        $item10 = new BakedItem();
        $item11 = new BakedItem();
        $item12 = new BakedItem();
        $item13 = new BakedItem();
        $item14 = new BakedItem();
        $item15 = new BakedItem();
        $item16 = new BakedItem();
        $item17 = new BakedItem();

        $item1->setDescription('2 Dozen Brownies-Saturday drop off')
            ->setQuantity(15);
        $item2->setDescription('2 Dozen Sugar Free Cookies-Friday drop off')
            ->setQuantity(7);
        $item3->setDescription('2 Dozen Sugar Free Cookies-Saturday drop off')
            ->setQuantity(7);
        $item4->setDescription('2 Dozen Fudge-Friday drop off')
            ->setQuantity(14);
        $item5->setDescription('2 Dozen Fudge-Saturday drop off')
            ->setQuantity(14);
        $item6->setDescription('2 Dozen Pralines-Friday drop off')
            ->setQuantity(12);
        $item7->setDescription('2 Dozen Pralines-Saturday drop off')
            ->setQuantity(12);
        $item8->setDescription('2 Dozen Rice Krispy Treats-Friday drop off')
            ->setQuantity(10);
        $item9->setDescription('2 Dozen Rice Krispy Treats-Saturday drop off')
            ->setQuantity(10);
        $item10->setDescription('2 Dozen Cake Balls-Friday drop off')
            ->setQuantity(5);
        $item11->setDescription('2 Dozen Cake Balls-Saturday drop off')
            ->setQuantity(5);
        $item12->setDescription('2 Dozen Popcorn Balls-Friday drop off')
            ->setQuantity(5);
        $item13->setDescription('2 Dozen Popcorn Balls-Saturday drop off')
            ->setQuantity(5);
        $item14->setDescription('2 Dozen Cupcakes-Friday drop off')
            ->setQuantity(10);
        $item15->setDescription('2 Dozen Cupcakes-Saturday drop off')
            ->setQuantity(10);
        $item16->setDescription('1 Whole Cake-Friday drop off')
            ->setQuantity(15);
        $item17->setDescription('1 Whole Cake-Saturday drop off')
            ->setQuantity(25);

        $manager->persist($item1);
        $manager->persist($item2);
        $manager->persist($item3);
        $manager->persist($item4);
        $manager->persist($item5);
        $manager->persist($item6);
        $manager->persist($item7);
        $manager->persist($item8);
        $manager->persist($item9);
        $manager->persist($item10);
        $manager->persist($item11);
        $manager->persist($item12);
        $manager->persist($item13);
        $manager->persist($item14);
        $manager->persist($item15);
        $manager->persist($item16);
        $manager->persist($item17);
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

