<?php

namespace CB\FairBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
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
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('UserBundle:User')->findUsersWithBoothTimes();

//        $objExcel = new \PHPExcel();
//        $objExcel->getSheet(0)->setTitle('Booths');
//
//        $objExcel->getProperties()->setCreator("Craig Baker")
//            ->setLastModifiedBy("Craig Baker")
//            ->setTitle("2013 Fair - Booths")
//            ->setSubject("2013 Fair - Booths")
//            ->setDescription("2013 Fair - Booths");
//
//        foreach($users as $user) {
//            $objExcel->setActiveSheetIndex(0)
//                ->setCellValue('A1', '2013 Booths')
//                ->setCellValue('A2', 'LIST OF BOOTHS');
//        }
//
//
//        $objWriter = new \PHPExcel_Writer_Excel2007($objExcel);
//        $objWriter->save("booths.xlsx");

        var_dump($users);

        return array();
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

        $excel = new PHPExcel();
        $excel->getSheet(0)->setTitle('Booths');
        $excel->getProperties()->setCreator("Craig Baker")
            ->setLastModifiedBy("Craig Baker")
            ->setTitle("2013 Fair - Report")
            ->setSubject("2013 Fair - Report")
            ->setDescription("2013 Fair - Report");

        $headers = array('Student', 'HR', 'Parent', 'Phone', 'Name of Booth', 'Day', 'Time');
        $row = 1;
        foreach($headers as $col => $header) {
            $excel->getActiveSheet()
                ->setCellValueByColumnAndRow($col, $row, $header)
                ->getStyleByColumnAndRow($col, $row)
                ->getFont()->setBold(true);
            switch($col) {
                case 0:
                    $width = 27.0;
                    break;
                case 1:
                    $width = 3.0;
                    break;
                case 2:
                    $width = 30.0;
                    break;
                case 3:
                    $width = 30.0;
                    break;
                case 4:
                    $width = 35.0;
                    break;
                case 5:
                    $width = 11.0;
                    break;
                case 6:
                    $width = 16.0;
                    break;
                default:
                    $width = 12.0;
                    break;
            }
            $excel->getActiveSheet()->getColumnDimensionByColumn($col)->setWidth($width);
        }
        foreach($users as $user) {
            $row += 1;
            $col = 0;
            foreach($user as $name=>$item) {
                if ($name == 'time') {
                    /** var \DateTime $item **/
                    $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $item->format('l'));
                    $col += 1;

                    $nextHour = new \DateTime($item->format('Y-m-d H:i:s'));
                    $nextHour->add(new \DateInterval('PT0' . $user['duration'] . 'H'));

                    $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $item->format('g A').' - '.$nextHour->format('g A'));
                } elseif ($name != 'duration') {
                    $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $item);
                    $col += 1;
                }
            }
        }

        $excel->getSheet(0)->setAutoFilterByColumnAndRow(0,1,$col,$row);
//        print_r($excel->getActiveSheet()->getAutoFilter());die();

        $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
        $writer->save(__DIR__.'/../../../../web/uploads/documents/booths report.xlsx');
        return new BinaryFileResponse(
            __DIR__.'/../../../../web/uploads/documents/booths_report.xls',
            200,
            array(
                'Content-Type'          => 'application/xlsx',
                'Content-Disposition'   => 'attachment; filename="Fair 2013 - Booths.xlsx"'
            )
        );
    }

}
