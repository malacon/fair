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
     * @Template("FairBundle:Default:index.html.twig")
     */
    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Get Booths
        /** @var \CB\FairBundle\Entity\BoothRepository $boothRepo */
        $boothRepo = $this->getDoctrine()->getRepository('FairBundle:Booth');
        $booths = $boothRepo->findAll();

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
     * Retrieves the list of basic users
     *
     * @Route("/getUser.{_format}", name="get_user", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function getUsersJsonAction()
    {
        $user = $this->getRequest()->request->get('user');
        /** @var \CB\UserBundle\Entity\UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository('UserBundle:User');
        $users = $userRepo->findByUsernameChunk($user);

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($users);
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @Route("/isUser.{_format}", name="is_user", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function isUserAction()
    {
        $user = $this->getRequest()->request->get('user');
        /** @var \CB\UserBundle\Entity\UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository('UserBundle:User');
        $isUser = $userRepo->isUser($user);
        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse(array('isUser' => $isUser));
        }
        return $this->redirect($this->generateUrl('home'));
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
