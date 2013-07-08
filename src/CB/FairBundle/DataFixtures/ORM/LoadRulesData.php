<?php
namespace CB\FairBundle\DataFixtures\ORM;

use CB\FairBundle\Entity\Rule;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

class LoadRulesData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $rule1 = new Rule();
        $rule1->setNumberOfTimes(10);
        $rule1->setDescription("Default Rule");

        $rule2 = new Rule();
        $rule2->setNumberOfTimes(8);
        $rule2->setNumberOfAuctionItems(2);
        $rule2->setDescription("Auction Item Rule");

        $rule3 = new Rule();
        $rule3->setNumberOfTimes(8);
        $rule3->setNumberOfBakedItems(1);
        $rule3->setNumberOfAuctionItems(1);
        $rule3->setDescription("Baked Goods Rule");


        $manager->persist($rule1);
        $manager->persist($rule2);
        $manager->persist($rule3);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 1;
    }
}

