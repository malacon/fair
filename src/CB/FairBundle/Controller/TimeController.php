<?php

namespace CB\FairBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CB\FairBundle\Entity\Time;
use CB\FairBundle\Form\TimeType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Time controller.
 *
 * @Route("/time")
 */
class TimeController extends Controller
{

    /**
     * Lists all Time entities.
     *
     * @Route("/", name="time")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FairBundle:Time')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Time entity.
     *
     * @Route("/", name="time_create")
     * @Method("POST")
     * @Template("FairBundle:Time:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Time();
        $form = $this->createForm(new TimeType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('time_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Time entity.
     *
     * @Route("/new", name="time_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Time();
        $form   = $this->createForm(new TimeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Time entity.
     *
     * @Route("/{id}", name="time_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Time')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Time entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Time entity.
     *
     * @Route("/{id}/edit", name="time_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Time')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Time entity.');
        }

        $editForm = $this->createForm(new TimeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Time entity.
     *
     * @Route("/{id}", name="time_update")
     * @Method("PUT")
     * @Template("FairBundle:Time:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Time')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Time entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TimeType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('time_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Time entity.
     *
     * @Route("/{id}", name="time_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FairBundle:Time')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Time entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @param $id
     *
     * @Route("/family.{_format}", name="booth_family", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function getFamilyDataJson()
    {
        $data = array();

        $this->checkUserPassed();
        $data['quantities'] = array(
            'hours' => $this->getUser()->getNumOfHours(),
            'bakedItems' => $this->getUser()->hasBakedItem(),
            'auctionItems' => $this->getUser()->getSaleItems(),
        );
        $data['isPassed'] = $this->getUser()->getIsPassedRules();
        $data['timestamps'] = $this->getUser()->getTimestamps();

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Assigns worker to booth time
     *
     * @Route("/{id}/{boothId}/work.{_format}", name="booth_work", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function workBoothTimeAction($id, $boothId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $time \CB\FairBundle\Entity\Time $entity */
        $time = $em->getRepository('FairBundle:Time')->find($id);

        /** @var \CB\FairBundle\Entity\BoothRepository $boothRepo */
        $boothRepo = $this->getDoctrine()->getRepository('FairBundle:Booth');
        $booth = $boothRepo->find($boothId);

        /** @var \CB\UserBundle\Entity\User $spouse */
        $spouse = $this->getUser()->getSpouse($this->getRequest()->query->get('spouse'));
        $data['spouse'] = $spouse->getId();

        if (!$time) {
            throw $this->createNotFoundException();
        }


        // Add the worker to the time
        if ($time->isWorkerAlreadySignedUpAtThisTime($spouse)) {
            $time->removeWorker($spouse);
        } else {
            $time->addWorker($spouse);
        }

        $data = $this->getBaseData($time);

        // Check to see if the user now passes
        $this->checkUserPassed();
        $data['isPassed'] = $this->getUser()->getIsPassedRules();
        $data['family'] = $this->getUser()->getName();
        $data['boothHours'] = $spouse->getNumOfHoursByBooth();
        $data['spouseHours'] = $this->getUser()->getNumOfHoursBySpouse();
        $data['html'] = $this->renderView('FairBundle:Default:booth.html.twig',
            array(
                'booth' => $booth,
                'spouse' => $spouse,
            )
        );

        $em->persist($time);
        $em->persist($spouse);
        $em->flush();


        if($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
        }
        return $this->redirect($this->generateUrl('home'));

    }

    /**
     * @Route("/isPassed.{_format}", name="is_passed", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function getIsPassed()
    {
        $this->checkUserPassed();
        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse(
                array(
                    'isPassed' => $this->getUser()->getIsPassedRules(),
                    'quantities' => array(
                        'hours' => $this->getUser()->getNumOfHours(),
                        'baked' => $this->getUser()->isBaking(),
                        'auction' => $this->getUser()->getNumOfSaleItems(),
                    ),
                )
            );
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Unassigns worker to booth time
     *
     * @Route("/{id}/unwork.{_format}", name="booth_unwork", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function unworkBoothTimeAction($id, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $time \CB\FairBundle\Entity\Time $entity */
        $time = $em->getRepository('FairBundle:Time')->find($id);

        if (!$time) {
            throw $this->createNotFoundException();
        }

        $data = $this->getBaseData($time);

        /** @var \CB\UserBundle\Entity\User $spouse */
        $spouse = $this->getUser()->getSpouse($this->getRequest()->request->get('spouse'));

        // Remove the worker to the time
        if ($time->removeWorker($spouse)) {
            $data['userChanged'] = true;
            $data['userRemoved'] = true;
            $data['timeFilled'] = $time->isFilled();
            $data['timeWorked'] = $time->isWorkerAlreadySignedUpAtThisTime($spouse);
            $data['quantities']['hours'] = $this->getUser()->getNumOfHours();

            // Check to see if the user now passes
            $this->checkUserPassed();
            $data['isPassed'] = $this->getUser()->getIsPassedRules();
            $data['timestamps'] = $this->getUser()->getTimestamps();
        }

        $em->persist($time);
        $em->persist($spouse);
        $em->flush();

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @param \CB\FairBundle\Entity\Time $time
     * @return array
     */
    private function getBaseData($time)
    {
        return array(
            'id' => $time->getId(),
            'timestamp' => $time->getTime()->getTimestamp(),
            'timeFilled' => false,
            'timeWorked' => false,
            'userChanged' => false,
            'userAdded' => false,
            'userRemoved' => false,
            'quantities' => array(
                'hours' => $this->getUser()->getNumOfHours(),
                'bakedItems' => $this->getUser()->hasBakedItem(),
                'auctionItems' => $this->getUser()->getSaleItems(),
            ),
            'isPassed' => $this->getUser()->getIsPassedRules(),
        );

    }

    /**
     * Creates a form to delete a Time entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
