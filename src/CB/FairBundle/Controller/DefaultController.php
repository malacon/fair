<?php

namespace CB\FairBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        // Get User's AuctionItems, BakedItems, and BoothTimes

        // Get Booths
        $repository = $this->getDoctrine()->getRepository('FairBundle:Booth');
        $booths = $repository->findAll();

        return array('booths' => $booths);
    }
}
