<?php

namespace CB\FairBundle\Controller;

use CB\FairBundle\Entity\Time;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CB\FairBundle\Entity\Booth;
use CB\FairBundle\Form\BoothType;

/**
 * Booth controller.
 *
 * @Route("/admin/booth")
 */
class BoothController extends Controller
{

    /**
     * Lists all Booth entities.
     *
     * @Route("/", name="booth")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FairBundle:Booth')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Booth entity.
     *
     * @Route("/", name="booth_create")
     * @Method("POST")
     * @Template("FairBundle:Booth:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Booth();
        $form = $this->createForm(new BoothType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('booth_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Booth entity.
     *
     * @Route("/new", name="booth_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Booth();

        $time1 = new Time();
        $time1->setTime(new \DateTime('2013-09-07'));
        $time1->setBooth($entity);
        $entity->getTimes()->add($time1);

        $form   = $this->createForm(new BoothType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Booth entity.
     *
     * @Route("/{id}", name="booth_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Booth')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Booth entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Booth entity.
     *
     * @Route("/{id}/edit", name="booth_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Booth')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Booth entity.');
        }

        $editForm = $this->createForm(new BoothType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Booth entity.
     *
     * @Route("/{id}", name="booth_update")
     * @Method("PUT")
     * @Template("FairBundle:Booth:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Booth')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Booth entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new BoothType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('booth_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Booth entity.
     *
     * @Route("/{id}", name="booth_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FairBundle:Booth')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Booth entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('booth'));
    }


    /**
     * Creates a form to delete a Booth entity by id.
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
