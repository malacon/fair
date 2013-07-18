<?php

namespace CB\FairBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Time controller.
 *
 * @Route("/admin")
 */
class AdminPanelController extends Controller
{
    /**
     * @Route("/", name="admin_panel")
     * @Template()
     */
    public function defaultAction()
    {

    }

    /**
     * @Route("/settings")
     * @Template()
     */
    public function configAction()
    {
    }

}
