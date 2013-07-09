<?php

namespace CB\FairBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/{type}/add.{_format}", name="auction_add", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function addAuctionItemAction($type)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var \CB\FairBundle\Entity\AuctionItem $auctionItem */
        $auctionItem = new AuctionItem();
        $auctionItem->setType($type);
        $this->getUser()->addAuctionItem($auctionItem);

        $em->persist($auctionItem);
        $em->persist($this->getUser());
        $em->flush();

        $this->checkUserPassed();
        $data = $this->getBaseData($auctionItem);
        $data['isRemoved'] = false;
        $data['numAuctions'] = $this->getUser()->getNumOfAuctionItems();
        $data['isPassed'] = $this->getUser()->getIsPassedRules();
        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @Route("/{id}/remove.{_format}", name="auction_remove", defaults={"_format" = "json"}, requirements={"_format"="html|json"})
     */
    public function removeAuctionItemAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FairBundle:AuctionItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuctionItem entity.');
        }

        $data = $this->getBaseData($entity);
        $em->remove($entity);
        $em->flush();

        $this->checkUserPassed();
        $data['isRemoved'] = true;
        $data['numAuctions'] = $this->getUser()->getNumOfAuctionItems();
        $data['isPassed'] = $this->getUser()->getIsPassedRules();

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createJsonResponse($data);
        }

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @param AuctionItem $auctionItem
     * @return array
     */
    private function getBaseData($auctionItem)
    {
        return array(
            'id' => $auctionItem->getId(),
            'description' => $auctionItem->__toString(),
            'url' => $this->generateUrl('auction_remove', array('id' => $auctionItem->getId())),
            'isPassed' => $this->getUser()->getIsPassedRules(),
        );
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
