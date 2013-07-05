<?php
namespace CB\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use CB\UserBundle\Entity\User;

class LoadUserData implements FixtureInterface
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

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}

