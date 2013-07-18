<?php

namespace CB\FairBundle\Controller;

use CB\FairBundle\Entity\Booth;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CB\FairBundle\Form\DocumentType;
use CB\FairBundle\Entity\Document;

use Ddeboer\DataImport\Reader\ExcelReader;

/**
 * Document controller.
 *
 * @Route("/admin/docs")
 */
class DocumentController extends Controller
{


    /**
     * @Route("/csv/{id}", name="load_csv")
     * @Template()
     */
    public function loadCSVAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \CB\FairBundle\Entity\Document $document */
        $document = $em->getRepository('FairBundle:Document')->find($id);

        // Create and configure the reader
        $excelReader = new ExcelReader(new \SplFileObject($document->getAbsolutePath()));
        $excelReader->setHeaderRowNumber(0);

        $booths = array();
        foreach ($excelReader as $key => $row) {
            $booth = new Booth();
            foreach ($row as $key1 => $value) {
                switch (strtolower($key1)) {
                    case 'booth':
                        $booth->$key($value);
                        break;
                    case 'description':
//                        $booth['description'] = ($value);
                        break;
                    case 'Location':
                        $booth->setLocation($value);
                        break;
                    default:

                        break;
                }

            }

            $booths[$key] = $booth;
        }
        $headers = $excelReader->getColumnHeaders();
        $rows = $excelReader->getFields();

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        return array(
            'doc' => $document,
            'headers' => $headers,
            'fields' => $rows,
            'reader' => $excelReader,
            'booths' => $booths,
        );
    }

    /**
     * Lists all Document entities.
     *
     * @Route("/", name="admin_docs")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \CB\FairBundle\Entity\DocumentRepository $repo */
        $repo = $em->getRepository('FairBundle:Document');
        // Newest first
        $entities = $repo->findBy(array(), array('created' => 'DESC'));

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Document entity.
     *
     * @Route("/", name="admin_docs_create")
     * @Method("POST")
     * @Template("FairBundle:Document:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Document();
        $form = $this->createForm(new DocumentType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_docs'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Document entity.
     *
     * @Route("/new", name="admin_docs_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Document();
        $form   = $this->createForm(new DocumentType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/{id}", name="admin_docs_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $editForm = $this->createForm(new DocumentType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Document entity.
     *
     * @Route("/{id}/edit", name="admin_docs_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $editForm = $this->createForm(new DocumentType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Document entity.
     *
     * @Route("/{id}", name="admin_docs_update")
     * @Method("PUT")
     * @Template("FairBundle:Document:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FairBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DocumentType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_docs'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Document entity.
     *
     * @Route("/{id}", name="admin_docs_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FairBundle:Document')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Document entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_docs'));
    }

    /**
     * Creates a form to delete a Document entity by id.
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
