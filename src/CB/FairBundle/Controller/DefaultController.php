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

        $originalBakedItems = array();
        foreach($this->getUser()->getBakedItems() as $item) {
            $originalBakedItems[] = $item;
        }
        $originalAuctionItems = array();
        foreach($this->getUser()->getAuctionItems() as $item) {
            $originalAuctionItems[] = $item;
        }

        $form = $this->createForm(new RegisterType(), $this->getUser());

        $form->handleRequest($request);

        if ($form->isValid()) {
            foreach ($this->getUser()->getBakedItems() as $item) {
                foreach ($originalBakedItems as $key => $toDel) {
                    if ($toDel->getId() === $item->getId()) {
                        unset($originalBakedItems[$key]);
                    }
                }
            }

            foreach ($originalBakedItems as $item) {
                $em->remove($item);
            }

            foreach ($this->getUser()->getAuctionItems() as $item) {
                foreach ($originalAuctionItems as $key => $toDel) {
                    if ($toDel->getId() === $item->getId()) {
                        unset($originalAuctionItems[$key]);
                    }
                }
            }

            foreach ($originalAuctionItems as $item) {
                $em->remove($item);
            }

            $this->setUserPassed();

            $em->persist($this->getUser());
            $em->flush();
        }

        return array(
            'booths' => $booths,
            'form' => $form->createView()
        );
    }

    /**
     * Retrieves the list of basic users
     *
     * @Route("/getUser.{_format}", name="get_user", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function getUsersJsonAction()
    {
//        $user = $this->getRequest()->request->get('user');
        $user = $this->getRequest()->request->get('user');
        /** @var \CB\UserBundle\Entity\UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository('UserBundle:User');
        $users = $userRepo->findByUsernameChunk($user);

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createWorkingJson($users);
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
            return $this->createWorkingJson(array('isUser' => $isUser));
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
            'auction' => $this->getUser()->getNumOfAuctionItems(),
            'baked' => $this->getUser()->getNumOfBakedItems(),
        );
        if($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createWorkingJson($data);
        }
        return $this->redirect($this->generateUrl('home'));
    }

    private function createWorkingJson(Array $list)
    {
        $response = new Response(json_encode($list, 0));

        return $response;
    }

}
