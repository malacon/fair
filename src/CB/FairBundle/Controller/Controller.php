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

    protected function setUserPassed()
    {
        /** @var \CB\FairBundle\Entity\RuleRepository $rulesRepo */
        $rulesRepo = $this->getDoctrine()->getRepository('FairBundle:Rule');
        $this->getUser()->setIsPassedRules($rulesRepo->passesRule(
            $this->getUser()->getNumOfHours(),
            $this->getUser()->getNumOfBakedItems(),
            $this->getUser()->getNumOfAuctionItems()
        ));
    }

}