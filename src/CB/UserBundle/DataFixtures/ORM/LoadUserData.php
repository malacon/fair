<?php
namespace CB\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use CB\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername('admin');
        $user1->setPlainPassword('admin');
        $user1->setFamilyName('Admin');
        $user1->setEmail('craig.d.baker@gmail.com');
        $user1->setEnabled(true);
        $user1->setRoles(array('ROLE_ADMIN'));

        $user2 = new User();
        $user2->setUsername('baker');
        $user2->setPlainPassword('baker');
        $user2->setFamilyName('Baker');
        $user2->setEmail('craig.d.baker+test@gmail.com');
        $user2->setEnabled(true);
        $user2->setRoles(array('ROLE_USER'));
        $user2->addChild('Samantha', 'K');
        $user2->addChild('Lucy', '2');

        $user3 = new User();
        $user3->setUsername('done');
        $user3->setPlainPassword('done');
        $user3->setFamilyName('Doner');
        $user3->setEmail('craig.d.baker+done@gmail.com');
        $user3->setEnabled(true);
        $user3->setRoles(array('ROLE_USER'));
        $user3->addChild('Ruth', '3');
        $user3->addChild('Bobby', '8');

        $this->addReference('user-user', $user2);
        $this->addReference('user-done', $user3);

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 10;
    }
}

