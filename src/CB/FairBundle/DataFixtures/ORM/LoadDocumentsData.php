<?php
namespace CB\FairBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CB\FairBundle\Entity\Document;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadDocumentsData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $usersDoc = new Document();
        $usersDoc->setName('Users');
        $usersDoc->setType('xls');
        $usersDoc->setPath('Fair Users Test.xlsx');

        $boothDoc = new Document();
        $boothDoc->setName('Booths');
        $boothDoc->setType('xls');
        $usersDoc->setPath('booths.xls');


        $manager->persist($boothDoc);
        $manager->persist($usersDoc);
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

