<?php

namespace CB\FairBundle\Controller;

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
        return array();
    }

    /**
     * @Route("/settings")
     * @Template()
     */
    public function configAction()
    {
        return array();
    }

    /**
     * Retrieves the list of basic users
     *
     * @Route("/getUser.{_format}", name="get_user", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function getUsersJsonAction()
    {
        $user = $this->getRequest()->request->get('user');
        /** @var \CB\UserBundle\Entity\FamilyRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository('UserBundle:Family');
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
        /** @var \CB\UserBundle\Entity\FamilyRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository('UserBundle:Family');
        $isUser = $userRepo->isUser($user);
        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse(array('isUser' => $isUser));
        }
        return $this->redirect($this->generateUrl('home'));
    }



}
