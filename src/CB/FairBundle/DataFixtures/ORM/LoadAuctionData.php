<?php
namespace CB\FairBundle\DataFixtures\ORM;

use CB\FairBundle\Entity\AuctionItem;
use CB\FairBundle\Entity\BakedItem;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

class LoadAuctionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var \CB\UserBundle\Entity\User $user */
        $user = $this->getReference('user-user');

        $item1 = new AuctionItem();
        $item1->setType('craft');
        $user->addAuctionItem($item1);

        $manager->persist($user);
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

