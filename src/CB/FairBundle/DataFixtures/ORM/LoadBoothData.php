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
        $booth1->setWorkerLimit(1);

        $spouses = $family1->getSpouses()->getValues();
        $spouse = $spouses[0];
        $spouse2 = $spouses[1];
        // Friday
        for ($i = 16; $i < 21; $i++) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-13 '. $i .':00:00'));
            $time->setBooth($booth1);
            $booth1->addTime($time);
            $manager->persist($time);
        }

        // Saturday
        for ($i = 8; $i < 22; $i++) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-14 '. $i .':00:00'));
            $time->setBooth($booth1);
            if ($i == 20 || $i == 21) {
                $time->addWorker($spouse);
            }
            if ($i >8 && $i < 20) {
                $time->addWorker($spouse2);
            }
            $booth1->addTime($time);
            $manager->persist($time);
        }

        $manager->persist($spouse);
        $manager->persist($spouse2);

        // Sunday
        for ($i = 8; $i < 16; $i++) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-15 '. $i .':00:00'));
            $time->setBooth($booth1);
            $booth1->addTime($time);
            $manager->persist($time);
        }

        $booth2 = new Booth();
        $booth2->setName('Cake Walk');
        $booth2->setDescription('Walk around the circle.  Win a cake or two!');
        $booth2->setLocation('Gym');
        $booth2->setWorkerLimit(4);

        $family1->getSpouses()->next();
        $spouse = $family1->getSpouses()->first();
        for ($i = 8; $i < 21; $i++) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-14 '. $i .':00:00'));
            $time->setBooth($booth2);
            if ($i == 16 || $i == 18) {
                $time->addWorker($spouse);
            }
            $booth2->addTime($time);
            $manager->persist($time);
        }

        $manager->persist($spouse);
        $manager->persist($spouse2);

        $booth3 = new Booth();
        $booth3->setName('Ring Toss');
        $booth3->setDescription('Toss your rings and get a prize!');
        $booth3->setLocation('Field');
        $booth3->setWorkerLimit(4);

        for ($i = 8; $i < 21; $i++) {
            $time = new Time();
            $time->setTime(new \DateTime('2013-09-14 '. $i .':00:00'));
            $time->setBooth($booth3);
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

