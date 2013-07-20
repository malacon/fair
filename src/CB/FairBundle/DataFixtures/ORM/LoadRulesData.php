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
        $rule1->setDescription("Choice 1");

        $rule2 = new Rule();
        $rule2->setNumberOfTimes(8);
        $rule2->setNumberOfAuctionItems(2);
        $rule2->setDescription("Choice 3");

        $rule3 = new Rule();
        $rule3->setNumberOfTimes(8);
        $rule3->setNumberOfBakedItems(1);
        $rule3->setNumberOfAuctionItems(1);
        $rule3->setDescription("Choice 2");


        $manager->persist($rule1);
        $manager->persist($rule3);
        $manager->persist($rule2);
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

