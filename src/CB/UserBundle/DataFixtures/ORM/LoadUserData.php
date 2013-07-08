<?php
namespace CB\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use CB\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface container */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        /** @var \Symfony\Component\DependencyInjection\ContainerInterface container */
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');

        $user1 = new User();
        $user1->setUsername('admin');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user1);
        $user1->setPassword($encoder
            ->encodePassword('admin', $user1->getSalt()));
        $user1->setFamilyName('Admin');
        $user1->setEmail('craig.d.baker@gmail.com');
        $user1->setEnabled(true);
        $user1->setRoles(array('ROLE_ADMIN'));

        $user2 = new User();
        $user2->setUsername('baker');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user2);
        $user2->setPassword($encoder
            ->encodePassword('baker', $user2->getSalt()));
        $user2->setFamilyName('Baker');
        $user2->setEmail('craig.d.baker+test@gmail.com');
        $user2->setEnabled(true);
        $user2->setRoles(array('ROLE_USER'));
        $user2->addChild('Samantha', 'K');
        $user2->addChild('Lucy', '2');

        $user3 = new User();
        $user3->setUsername('done');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user3);
        $user3->setPassword($encoder
            ->encodePassword('done', $user3->getSalt()));
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

