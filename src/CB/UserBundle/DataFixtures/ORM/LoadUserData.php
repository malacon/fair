<?php
namespace CB\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use CB\UserBundle\Entity\Family;
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

        $family1 = new Family();
        $parent1 = new User('Craig');
        $parent2 = new User('Christina');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($family1);
        $family1->setUsername('baker')
            ->setName('Baker')
            ->setEldest('Lucy')
            ->setEldestGrade('2A')
            ->setEmail('craig.d.baker+fair@gmail.com')
            ->setPassword($encoder
                ->encodePassword('baker', $family1->getSalt()))
            ->setEnabled(true)
            ->setRoles(array('ROLE_USER'))
            ->addSpouse($parent1)
            ->addSpouse($parent2)
            ->setTimeToLogin(new \DateTime('2013-08-01'))
        ;
        $manager->persist($parent1);
        $manager->persist($parent2);

        $family2 = new Family();
        $parent1 = new User('Jim');
        $parent2 = new User('Shelia');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($family2);
        $family2->setUsername('smith')
            ->setName('Smith')
            ->setEldest('Gillian')
            ->setEldestGrade('6A')
            ->setEmail('craig.d.baker+fair2@gmail.com')
            ->setPassword($encoder
                ->encodePassword('smith', $family2->getSalt()))
            ->setEnabled(true)
            ->setRoles(array('ROLE_USER'))
            ->addSpouse($parent1)
            ->addSpouse($parent2)
            ->setTimeToLogin(new \DateTime())
        ;
        $manager->persist($parent1);
        $manager->persist($parent2);

        $family3 = new Family();
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($family3);
        $family3->setUsername('admin')
            ->setName('Admin')
            ->setEmail('craig.d.baker@gmail.com')
            ->setPassword($encoder
                ->encodePassword('admin', $family3->getSalt()))
            ->setEnabled(true)
            ->setRoles(array('ROLE_ADMIN'))
            ->setTimeToLogin(new \DateTime('now'))
        ;

        $this->addReference('user-user', $family1);
        $this->addReference('user-done', $family2);

        $manager->persist($family1);
        $manager->persist($family2);
        $manager->persist($family3);
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

