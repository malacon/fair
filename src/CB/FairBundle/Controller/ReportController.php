<?php

namespace CB\FairBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Report controller.
 *
 * @Route("/admin/reports")
 */
class ReportController extends Controller
{
    /**
     * @Route("/", name="admin_reports")
     * @Template()
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        return array();
    }

    /**
     *
     * @Route("/report/booth", name="admin_reports_booths")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function boothReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $families = $em->getRepository('UserBundle:Family')->findAllUsersNotAdmins();

        return array(
            'families' => $families,
        );
    }

    /**
     *
     * @Route("/report/baked", name="admin_reports_baked")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function bakedReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $families = $em->getRepository('UserBundle:Family')->findAllUsersBaking();

        return array(
            'families' => $families,
        );
    }

    /**
     *
     * @Route("/report/auction", name="admin_reports_auction")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function auctionReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $families = $em->getRepository('UserBundle:Family')->findAllUsersNotAdmins();

        return array(
            'families' => $families,
        );
    }
}
