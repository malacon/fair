<?php

namespace CB\FairBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template("FairBundle:Default:index.html.twig")
     */
    public function registerAction()
    {
        // Get User's AuctionItems, BakedItems, and BoothTimes

        // Get Booths
        $repository = $this->getDoctrine()->getRepository('FairBundle:Booth');
        $booths = $repository->findAll();

        $form = $this->createFormBuilder()
            ->add('register', 'submit')
            ->getForm();

        return array('booths' => $booths, 'form' => $form->createView());
    }
}
