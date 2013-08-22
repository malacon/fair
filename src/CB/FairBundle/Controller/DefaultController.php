<?php

namespace CB\FairBundle\Controller;

use CB\FairBundle\Form\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function defaultAction(Request $request)
    {
        if ($this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin_panel'));
        }

        if (!$this->getUser()->isTimeToLogin()) {
            return $this->redirect($this->generateUrl('fos_user_security_logout'));
        }

        print_r($this->getUser()->getTimeToLogin());

        // Get Booths
        /** @var \CB\FairBundle\Entity\BoothRepository $boothRepo */
        $boothRepo = $this->getDoctrine()->getRepository('FairBundle:Booth');
        $booths = $boothRepo->findAllOrderByLocation();

        $bakedRepo = $this->getDoctrine()->getRepository('FairBundle:BakedItem');
        $bakedItems = $bakedRepo->findAll();

        $rules = $this->checkUserPassed();

        return array(
            'booths' => $booths,
            'bakedItems' => $bakedItems,
            'rules' => $rules,
        );
    }

    /**
     * @Route("/isUserPassed.{_format}", name="is_user_passed", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function isUserPassedAction()
    {
        $data = array(
            'isUserPassed' => $this->getUser()->getIsPassedRules(),
            'hours' => $this->getUser()->getNumOfHours(),
            'auction' => $this->getUser()->getNumOfSaleItems(),
            'baked' => $this->getUser()->hasBakedItem(),
        );
        if($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
        }
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @Route("/getBooth/{id}.{_format}", name="get_booth", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function boothAction($id)
    {
        $spouseId = $this->getRequest()->query->get('spouse');
        /** @var \CB\FairBundle\Entity\BoothRepository $boothRepo */
        $boothRepo = $this->getDoctrine()->getRepository('FairBundle:Booth');
        $booth = $boothRepo->find($id);

        /** @var \CB\UserBundle\Entity\User $spouse */
        $spouse = $this->getUser()->getSpouses()->get($spouseId);
        $this->checkUserPassed();

        $data = array(
            'isPassed' => $this->getUser()->getIsPassedRules(),
            'quantities' => array(
                'hours' => $this->getUser()->getNumOfHours(),
                'auction' => $this->getUser()->getNumOfSaleItems(),
                'baked' => $this->getUser()->hasBakedItem(),
            ),
            'boothHours' => $spouse->getNumOfHoursByBooth(),
            'spouseHours' => $this->getUser()->getNumOfHoursBySpouse(),
            'html' => $this->renderView('FairBundle:Default:booth.html.twig',
                array(
                    'booth' => $booth,
                    'spouse' => $spouse,
                )),
        );

        if($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
        }
        return $this->redirect($this->generateUrl('home'));

    }

}
