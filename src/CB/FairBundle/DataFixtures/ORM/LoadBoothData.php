<?php
namespace CB\FairBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CB\FairBundle\Entity\Booth;
use CB\FairBundle\Entity\Time;
use Doctrine\Common\Collections\ArrayCollection;

class LoadBoothData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var \CB\UserBundle\Entity\Family $family1 */
        $family1 = $this->getReference('user-user');
        /** @var \CB\UserBundle\Entity\Family $family2 */
        $family2 = $this->getReference('user-done');

        $booth1 = new Booth();
        $booth1->setName('Darts');
        $booth1->setDescription('Throwing darts at balloons.  What could be more fun?');
        $booth1->setLocation('Field');

        $spouses = $family1->getSpouses()->getValues();
        $spouse = $spouses[0];
        $spouse2 = $spouses[1];
        // Friday
        foreach (array(15, 19) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-13 '. $t .':00:00'));
            $time->setDuration(4);
            $time->setBooth($booth1);
            $time->setWorkerLimit(1);
            $booth1->addTime($time);
            $manager->persist($time);
        }

        // Saturday
        foreach (array(7, 13, 18) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-14 '. $t .':00:00'));
            $time->setDuration(5);
            $time->setBooth($booth1);
            $time->setWorkerLimit(1);
            $booth1->addTime($time);
            $manager->persist($time);
        }

        $manager->persist($spouse);
        $manager->persist($spouse2);

        // Sunday
        foreach (array(7, 13, 18) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-15 '. $t .':00:00'));
            $time->setDuration(5);
            $time->setBooth($booth1);
            $time->setWorkerLimit(1);
            $booth1->addTime($time);
            $manager->persist($time);
        }

        $booth2 = new Booth();
        $booth2->setName('Cake Walk');
        $booth2->setDescription('Walk around the circle.  Win a cake or two!');
        $booth2->setLocation('Gym');


        $family1->getSpouses()->next();
        $spouse = $family1->getSpouses()->first();
        // Friday
        foreach (array(15, 19) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-13 '. $t .':00:00'));
            $time->setDuration(4);
            $time->setBooth($booth2);
            $time->setWorkerLimit(1);
            $booth2->addTime($time);
            $manager->persist($time);
        }

        // Saturday
        foreach (array(7, 13, 18) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-14 '. $t .':00:00'));
            $time->setDuration(4);
            $time->setBooth($booth2);
            $time->setWorkerLimit(1);
            $booth2->addTime($time);
            $manager->persist($time);
        }

        // Sunday
        foreach (array(7, 13, 18) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-15 '. $t .':00:00'));
            $time->setDuration(4);
            $time->setBooth($booth1);
            $time->setWorkerLimit(2);
            $booth2->addTime($time);
            $manager->persist($time);
        }

        $manager->persist($spouse);
        $manager->persist($spouse2);

        $booth3 = new Booth();
        $booth3->setName('Ring Toss');
        $booth3->setDescription('Toss your rings and get a prize!');
        $booth3->setLocation('Field');


        // Friday
        foreach (array(15, 19) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-13 '. $t .':00:00'));
            $time->setDuration(4);
            $time->setBooth($booth1);
            $time->setWorkerLimit(3);
            $booth3->addTime($time);
            $manager->persist($time);
        }

        // Saturday
        foreach (array(7, 13, 18) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-14 '. $t .':00:00'));
            $time->setDuration(4);
            $time->setBooth($booth3);
            $time->setWorkerLimit(1);
            $booth3->addTime($time);
            $manager->persist($time);
        }

        $manager->persist($spouse);
        $manager->persist($spouse2);

        // Sunday
        foreach (array(7, 13, 18) as $t) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-15 '. $t .':00:00'));
            $time->setDuration(4);
            $time->setBooth($booth3);
            $time->setWorkerLimit(1);
            $booth3->addTime($time);
            $manager->persist($time);
        }


        $manager->persist($booth1);
        $manager->persist($booth2);
        $manager->persist($booth3);
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

