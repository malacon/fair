<?php

namespace CB\FairBundle\Controller;

use CB\FairBundle\Form\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template("FairBundle:Default:index.html.twig")
     */
    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Get Booths
        /** @var \CB\FairBundle\Entity\BoothRepository $boothRepo */
        $boothRepo = $this->getDoctrine()->getRepository('FairBundle:Booth');
        $booths = $boothRepo->findAll();

        $form = $this->createForm(new RegisterType(), $this->getUser());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($this->getUser());
            $em->flush();
        }

        return array('booths' => $booths, 'form' => $form->createView());
    }
}
