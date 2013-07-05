<?php

namespace CB\FairBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CB\FairBundle\Entity\AuctionItem;
use CB\FairBundle\Form\AuctionItemType;

/**
 * AuctionItem controller.
 *
 * @Route("/admin/auctionitem")
 */
class AuctionItemController extends Controller
{

    /**
     * Lists all AuctionItem entities.
     *
     * @Route("/", name="auctionitem")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FairBundle:AuctionItem')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new AuctionItem entity.
     *
     * @Route("/", name="auctionitem_create")
     * @Method("POST")
     * @Template("FairBundle:AuctionItem:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new AuctionItem();
        $form = $this->createForm(new AuctionItemType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('auctionitem_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new AuctionItem entity.
     *
     * @Route("/new", name="auctionitem_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AuctionItem();
        $form   = $this->createForm(new AuctionItemType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AuctionItem entity.
     *
     * @Route("/{id}", name="auctionitem_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:AuctionItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuctionItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AuctionItem entity.
     *
     * @Route("/{id}/edit", name="auctionitem_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:AuctionItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuctionItem entity.');
        }

        $editForm = $this->createForm(new AuctionItemType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing AuctionItem entity.
     *
     * @Route("/{id}", name="auctionitem_update")
     * @Method("PUT")
     * @Template("FairBundle:AuctionItem:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:AuctionItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuctionItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AuctionItemType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('auctionitem_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a AuctionItem entity.
     *
     * @Route("/{id}", name="auctionitem_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FairBundle:AuctionItem')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AuctionItem entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('auctionitem'));
    }

    /**
     * Creates a form to delete a AuctionItem entity by id.
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
