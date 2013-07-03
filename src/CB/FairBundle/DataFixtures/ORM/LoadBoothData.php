<?php
namespace CB\FairBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CB\FairBundle\Entity\Booth;
use CB\FairBundle\Entity\Time;
use Doctrine\Common\Collections\ArrayCollection;

class LoadBoothData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $booth1 = new Booth();
        $booth1->setName('Darts');
        $booth1->setDescription('Throwing darts at balloons.  What could be more fun?');
        $booth1->setLocation('Field');
        $booth1->setQuantity(4);

        $times1 = new ArrayCollection();

        for ($i = 8; $i < 21; $i++) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-07 '. $i .':00:00'));
            $time->setBooth($booth1);
            $booth1->addTime($time);
            $manager->persist($time);
        }

        $booth2 = new Booth();
        $booth2->setName('Cake Walk');
        $booth2->setDescription('Walk around the circle.  Win a cake or two!');
        $booth2->setLocation('Gym');
        $booth2->setQuantity(4);

        $times1 = new ArrayCollection();

        for ($i = 8; $i < 21; $i++) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-07 '. $i .':00:00'));
            $time->setBooth($booth2);
            $booth2->addTime($time);
            $manager->persist($time);
        }

        $booth3 = new Booth();
        $booth3->setName('Ring Toss');
        $booth3->setDescription('Toss your rings and get a prize!');
        $booth3->setLocation('Field');
        $booth3->setQuantity(4);

        $times1 = new ArrayCollection();

        for ($i = 8; $i < 21; $i++) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-07 '. $i .':00:00'));
            $time->setBooth($booth3);
            $booth3->addTime($time);
            $manager->persist($time);
        }


        $manager->persist($booth1);
        $manager->persist($booth2);
        $manager->persist($booth3);
        $manager->flush();
    }
}

