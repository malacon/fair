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
        $editForm->bind($request);

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
        $form->bind($request);

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
     * Assigns worker to booth time
     *
     * @Route("/{id}/work.{_format}", name="booth_work", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function workBoothTimeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $time \CB\FairBundle\Entity\Time $entity */
        $time = $em->getRepository('FairBundle:Time')->find($id);

        if (!$time) {
            throw $this->createNotFoundException();
        }

        $data = $this->getBaseData($time);

        // Add the worker to the time
        if ($time->addWorker($this->getUser())) {
            $data['userChanged'] = true;
            $data['userAdded'] = true;
            $data['timeFilled'] = $time->isFilled();
            $data['timeWorked'] = $time->isUserAlreadySignedUpAtThisTime($this->getUser());
            $data['quantities']['hours'] = $this->getUser()->getNumOfHours();

            // Check to see if the user now passes
            $this->checkUserPassed();
            $data['isPassed'] = $this->getUser()->getIsPassedRules();
            $data['timestamps'] = $this->getUser()->getTimestamps();
        } else {
            $data['timeFilled'] = $time->isFilled();
        }

        $em->persist($time);
        $em->persist($this->getUser());
        $em->flush();

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
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

        // Remove the worker to the time
        if ($time->removeWorker($this->getUser())) {
            $data['userChanged'] = true;
            $data['userRemoved'] = true;
            $data['timeFilled'] = $time->isFilled();
            $data['timeWorked'] = $time->isUserAlreadySignedUpAtThisTime($this->getUser());
            $data['quantities']['hours'] = $this->getUser()->getNumOfHours();

            // Check to see if the user now passes
            $this->checkUserPassed();
            $data['isPassed'] = $this->getUser()->getIsPassedRules();
            $data['timestamps'] = $this->getUser()->getTimestamps();
        }

        $em->persist($time);
        $em->persist($this->getUser());
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
                'auctionItems' => $this->getUser()->getAuctionItems(),
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
