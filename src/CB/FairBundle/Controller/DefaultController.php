<?php

namespace CB\FairBundle\Controller;

use CB\FairBundle\Form\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template("FairBundle:Default:index.html.twig")
     */
    public function registerAction()
    {
        // Get User's AuctionItems, BakedItems, and BoothTimes

        // Get Booths
        /** @var \CB\FairBundle\Entity\BoothRepository $boothRepo */
        $boothRepo = $this->getDoctrine()->getRepository('FairBundle:Booth');
        $booths = $boothRepo->findAll();

        /** @var \CB\FairBundle\Entity\AuctionItemRepository $auctionRepo */
        $auctionRepo = $this->getDoctrine()->getRepository('FairBundle:AuctionItem');
        $auctionItems = $auctionRepo->findByUser($this->getUser());

        /** @var \CB\FairBundle\Entity\BakedItemRepository $bakedRepo */
        $bakedRepo = $this->getDoctrine()->getRepository('FairBundle:BakedItem');
        $bakedItems = $bakedRepo->findByUser($this->getUser());

        return array('booths' => $booths, 'bakedItems' => $bakedItems, 'auctionItems' => $auctionItems);
    }
}
