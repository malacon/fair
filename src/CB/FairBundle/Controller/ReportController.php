<?php

namespace CB\FairBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use PHPExcel;
use PHPExcel_Worksheet;
use PHPExcel_IOFactory;

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
        $users = $em->getRepository('UserBundle:User')->findUsersWithBoothTimes();

        return array(
            'adults' => $users,
        );
    }


    /**
     * @Route("/report/booth/xls", name="admin_reports_booths_xls")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function createBoothXlsAction()
    {
        $objExcel = new \PHPExcel();
//        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
//        $objWriter->save("booths.xlsx");
//        $objPHPExcel->getSheet(0)->setTitle('Booths');
//
//        $objPHPExcel->getProperties()->setCreator("Craig Baker")
//            ->setLastModifiedBy("Craig Baker")
//            ->setTitle("2013 Fair - Booths")
//            ->setSubject("2013 Fair - Booths")
//            ->setDescription("2013 Fair - Booths");
//
//        $objPHPExcel->setActiveSheetIndex(0)
//            ->setCellValue('A1', '2013 Booths')
//            ->setCellValue('A2', 'LIST OF BOOTHS');
//



        $response = $excelService->getResponse();

//        $response->headers->set('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        $response->headers->set('Content-Disposition: attachment;filename="booths.xlsx"');
//        $response->headers->set('Cache-Control: max-age=0');

        return $response;
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

    /**
     * @Route("/report/test", name="admin_reports_test")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function testReportAction()
    {
//        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
//            ->from('UserBundle:User', 'u')
//            ->select('u.name as parent, f.username as child, f.eldestGrade as grade, u.phone, b.name, t.time, t.duration')
//            ->innerJoin('u.times', 't')
//            ->innerJoin('t.booth', 'b')
//            ->innerJoin('u.family', 'f')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('UserBundle:User')->findUsersWithBoothTimes();
        var_dump($users);die();
    }

}
