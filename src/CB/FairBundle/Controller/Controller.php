<?php

namespace CB\FairBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    protected function getSecurityContext()
    {
        return $this->container->get('security.context');
    }

    /**
     * @return \CB\UserBundle\Entity\User
     */
    public function getUser()
    {
        return parent::getUser();
    }

    /**
     * Checks to see if the user is passed and updates the isPassed value of the user
     */
    protected function checkUserPassed()
    {
        /** @var \CB\FairBundle\Entity\RuleRepository $rulesRepo */
        $rulesRepo = $this->getDoctrine()->getRepository('FairBundle:Rule');
        $rules = $rulesRepo->findAll();
        $this->getUser()->setIsPassedRules($rules);
        return $rules;
    }

}
