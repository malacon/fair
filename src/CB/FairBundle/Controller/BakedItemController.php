<?php

namespace CB\FairBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CB\FairBundle\Entity\BakedItem;
use CB\FairBundle\Form\BakedItemType;

/**
 * BakedItem controller.
 *
 * @Route("/admin/bakeditem")
 */
class BakedItemController extends Controller
{

    /**
     * Lists all BakedItem entities.
     *
     * @Route("/", name="bakeditem")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FairBundle:BakedItem')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new BakedItem entity.
     *
     * @Route("/", name="bakeditem_create")
     * @Method("POST")
     * @Template("FairBundle:BakedItem:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new BakedItem();
        $form = $this->createForm(new BakedItemType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bakeditem_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new BakedItem entity.
     *
     * @Route("/new", name="bakeditem_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new BakedItem();
        $form   = $this->createForm(new BakedItemType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a BakedItem entity.
     *
     * @Route("/{id}", name="bakeditem_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:BakedItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BakedItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing BakedItem entity.
     *
     * @Route("/{id}/edit", name="bakeditem_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:BakedItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BakedItem entity.');
        }

        $editForm = $this->createForm(new BakedItemType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing BakedItem entity.
     *
     * @Route("/{id}", name="bakeditem_update")
     * @Method("PUT")
     * @Template("FairBundle:BakedItem:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:BakedItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BakedItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new BakedItemType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bakeditem_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a BakedItem entity.
     *
     * @Route("/{id}", name="bakeditem_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FairBundle:BakedItem')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BakedItem entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('bakeditem'));
    }

    /**
     * Signs up the user for the baked item
     *
     * @Route("/{id}/bring.{_format}", name="baked_bring", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function bakedBringAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var \CB\FairBundle\Entity\BakedItem $bakedItem */
        $bakedItem = $em->getRepository('FairBundle:BakedItem')->find($id);

        if (!$bakedItem) {
            throw $this->createNotFoundException();
        }

        if ($this->getUser()->getBakedItem() == $bakedItem) {
            $this->getUser()->removeBakedItem();
        } else {
            $this->getUser()->setBakedItem($bakedItem);
        }

        $this->checkUserPassed();

        $em->persist($bakedItem);
        $em->persist($this->getUser());
        $em->flush();

        $filledItems = $em->getRepository('FairBundle:BakedItem')->getBakedItemsThatAreFilledAsArray();
        $data = $this->getBaseData($bakedItem, $filledItems);

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @param BakedItem $bakedItem
     * @return array
     */
    private function getBaseData($bakedItem, $unfilledItems)
    {
        return array(
            'id' => $bakedItem->getId(),
            'unavailableItems' => $unfilledItems,
            'description' => $bakedItem->getDescription(),
            'isItemAvailable' => $bakedItem->isItemAvailable(),
            'isWorkerBaking' => $this->getUser()->isBaking(),
            'isPassed' => $this->getUser()->getIsPassedRules(),
        );
    }

    /**
     * Creates a form to delete a BakedItem entity by id.
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
