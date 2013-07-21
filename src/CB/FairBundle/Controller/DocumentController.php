<?php

namespace CB\FairBundle\Controller;

use CB\FairBundle\Entity\Booth;
use CB\FairBundle\Entity\Time;
use CB\UserBundle\Entity\Family;
use CB\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CB\FairBundle\Form\DocumentType;
use CB\FairBundle\Entity\Document;

use Ddeboer\DataImport\Reader\ExcelReader;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Yaml\Yaml;

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
     * @Secure(roles="ROLE_ADMIN")
     */
    public function loadCSVAction($id)
    {
        /** @var \ $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \CB\FairBundle\Entity\Document $document */
        $document = $em->getRepository('FairBundle:Document')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        // Create and configure the reader
        $excelReader = new ExcelReader(new \SplFileObject($document->getAbsolutePath()));
        $excelReader->setHeaderRowNumber(9);
        for ($i = 0; $i<10; $i++) {
            $excelReader->next();
        }
        /** @var Array $arr */
        $arr = $excelReader->getRow(0);
        switch (reset($arr)) {
            case 'SJP & STS Fair Booths':
                $this->loadBoothInfo($excelReader, $em);
                break;
            case 'SJP & STS Fair Users':
                $this->loadUserInfo($excelReader, $em);
                break;
            default:
                break;
        }

        $document->setIsRun(true);
        $em->persist($document);
        $headers = $excelReader->getColumnHeaders();
        $rows = $excelReader->getFields();

        $em->flush();

        return array(
            'doc' => $document,
            'headers' => $headers,
            'fields' => $rows,
            'reader' => $excelReader,
        );
    }

    private function loadBoothInfo($excelReader, $em)
    {
        $booths = array();
        $settings = Yaml::parse(file_get_contents(__DIR__.'\..\Resources\config\settings.yml'));
        $settings = $settings['settings'];
        $dates = $settings['dates'];
        foreach ($excelReader as $key => $row) {
            if ($row['Name'] == '') {
                break;
            }
            $booth = new Booth();
            foreach ($row as $key1 => $value) {
                switch (strtolower($key1)) {
                    case 'name':
                        $booth->setName($value);
                        break;
                    case 'description':
                        $booth->setDescription($value);
                        break;
                    case 'location':
                        $booth->setLocation($value);
                        break;
                    case 'quantity':
                        $booth->setWorkerLimit($value);
                        break;
                    case 'day 1':
                        $timeRange = explode('-', $value);
                        $times = array();
                        for ($i = $timeRange[0]; $i<$timeRange[1]; $i++) {
                            $time = new Time();
                            $dtime = new \DateTime();
                            $dtime->setTimestamp($dates[0]);
                            $dtime->setTime($i, 0, 0);
                            $time->setTime($dtime);
                            $booth->addTime($time);
                            $times[] = $time;
                        }
                        break;
                    case 'day 2':
                        $timeRange = explode('-', $value);
                        $times = array();
                        for ($i = $timeRange[0]; $i<$timeRange[1]; $i++) {
                            $time = new Time();
                            $dtime = new \DateTime();
                            $dtime->setTimestamp($dates[1]);
                            $dtime->setTime($i, 0, 0);
                            $time->setTime($dtime);
                            $booth->addTime($time);
                            $times[] = $time;
                        }
                        break;
                    case 'day 3':
                        $timeRange = explode('-', $value);
                        $times = array();
                        for ($i = $timeRange[0]; $i<$timeRange[1]; $i++) {
                            $time = new Time();
                            $dtime = new \DateTime();
                            $dtime->setTimestamp($dates[2]);
                            $dtime->setTime($i, 0, 0);
                            $time->setTime($dtime);
                            $booth->addTime($time);
                            $times[] = $time;
                        }
                        break;
                    default:

                        break;
                }


            }
            $em->persist($booth);
            $booths[$key] = $booth;
        }
    }

    /**
     * @param $excelReader ExcelReader
     * @param $em
     */
    private function loadUserInfo($excelReader, $em)
    {
        $users = $em->getRepository('UserBundle:Family')->findAllUsersNotAdmins();
        foreach ($users as $user) {
            $em->remove($user);
        }
        $em->flush();
        
        $users = array();
        foreach ($excelReader as $key => $row) {
            if ($row['Family'] == '') {
                break;
            }
            $family = new Family();
            $encoder = $this->container
                ->get('security.encoder_factory')
                ->getEncoder($family);
            $family->setName($row['Family']);
            $family->setEldest($row['Eldest Child']);
            $family->setEldestGrade($row['Grade of Child']);
            $family->setUsername($row['Username']);
            $family->setPassword($encoder->encodePassword($row['Password'], $family->getSalt()));
            if (trim($row['Email']) != '')
                $family->setEmail($row['Email']);
            if (trim($row['Adult1']) != '') {
                $adult1 = new User($row['Adult1']);
                $family->addSpouse($adult1);
                $em->persist($adult1);
            }
            if (trim($row['Adult2']) != '') {
                $adult2 = new User($row['Adult2']);
                $family->addSpouse($adult2);
                $em->persist($adult2);
            }
            if (trim($row['Adult3']) != '') {
                $adult3 = new User($row['Adult3']);
                $family->addSpouse($adult3);
                $em->persist($adult3);
            }
            if (trim($row['Adult4']) != '') {
                $adult4 = new User($row['Adult4']);
                $family->addSpouse($adult4);
                $em->persist($adult4);
            }
            $family->setTimeToLogin(new \DateTime($row['Time to Signin'].'09:00:00'));
            $family->setRoles(array('ROLE_USER'));
            $family->setEnabled(true);
            $em->persist($family);
            $families[$key] = $family;
        }
    }

    /**
     * Lists all Document entities.
     *
     * @Route("/", name="admin_docs")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_ADMIN")
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
