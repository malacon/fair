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
        $em = $this->getDoctrine()->getManager();

        /** @var \CB\FairBundle\Entity\Document $document */
        $document = $em->getRepository('FairBundle:Document')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        // Create and configure the reader
        $excelReader = new ExcelReader(new \SplFileObject($document->getAbsolutePath()));
        $excelReader->setHeaderRowNumber(1);
        for ($i = 0; $i<10; $i++) {
            $excelReader->next();
        }
        /** @var Array $arr */
        $arr = $excelReader->getRow(0);
        switch (reset($arr)) {
            case 'SJP & STS Fair Booths':
                $this->loadBoothInfo($excelReader);
                break;
            case 'SJP & STS Fair Users':
                $this->loadUserInfo($excelReader);
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

    private function loadBoothInfo($excelReader)
    {
        $em = $this->getDoctrine()->getManager();
        $booths = $em->getRepository('FairBundle:Booth')->findAll();
        foreach ($booths as $booth) {
            $em->remove($booth);
        }
        $em->flush();
        $booths = array();
        $settings = Yaml::parse(file_get_contents(__DIR__.'\..\Resources\config\settings.yml'));
        $settings = $settings['settings'];
        $dates = $settings['dates'];
        var_dump($settings);
        foreach ($excelReader as $key => $row) {
            if ($row['Booth Name'] == '') {
                break;
            }
            if (!array_key_exists($row['Booth Name'], $booths)) {
                $booth = new Booth();
                $name = ucwords(strtolower($row['Booth Name']));
                $booth->setName($name);
                $booth->setDescription($name);
                $booth->setLocation($row['Location']);
            } else {
                $booth = $booths[$row['Booth Name']];
            }
            $time = new Time();
            if($row['Day 1'] !== null) {
                $timeRange = explode('-', $row['Day 1']);
                array_walk($timeRange, function(&$item) {$item = $item/100;});
                $time->setDuration($timeRange[1] - $timeRange[0]);
                $time->setWorkerLimit($row['Quantity']);

                $dtime = new \DateTime();
                $dtime->setTimestamp(strtotime($dates[0]));
                $dtime->setTime($timeRange[0], 0, 0);
                $time->setTime($dtime);
                $booth->addTime($time);
            } else if ($row['Day 2'] !== null) {
                $timeRange = explode('-', $row['Day 2']);
                array_walk($timeRange, function(&$item) {$item = $item/100;});
                $time->setDuration($timeRange[1] - $timeRange[0]);
                $time->setWorkerLimit($row['Quantity']);

                $dtime = new \DateTime();
                $dtime->setTimestamp(strtotime($dates[1]));
                $dtime->setTime($timeRange[0], 0, 0);
                $time->setTime($dtime);
                $booth->addTime($time);
            } else if($row['Day 3'] !== null) {
                $timeRange = explode('-', $row['Day 3']);
                array_walk($timeRange, function(&$item) {$item = $item/100;});
                $time->setDuration($timeRange[1] - $timeRange[0]);
                $time->setWorkerLimit($row['Quantity']);

                $dtime = new \DateTime();
                $dtime->setTimestamp(strtotime($dates[2]));
                $dtime->setTime($timeRange[0], 0, 0);
                $time->setTime($dtime);
                $booth->addTime($time);

            }
            $em->persist($time);
            $em->persist($booth);
            $em->flush();
            $booths[$row['Booth Name']] = $booth;
        }
    }

    /**
     * @param $excelReader ExcelReader
     * @param $em \
     */
    private function loadUserInfo($excelReader)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('UserBundle:Family')->findAllUsersNotAdmins();
        foreach ($users as $user) {
            $em->remove($user);
        }
        $em->flush();
        
        $families = array();
        foreach ($excelReader as $key => $row) {
            if ($row['Student Name'] == '') {
                break;
            }
            if (!array_key_exists((string) $row['Student ID'], $families)) {
                $family = new Family();
                $encoder = $this->container
                    ->get('security.encoder_factory')
                    ->getEncoder($family);
                $family->setName($row['Last']);
                $family->setEldest($row['First']);
                $family->setEldestGrade($row['Grade']);
                $family->setUsername($row['Student Name']);
                $family->setPassword($encoder->encodePassword($row['Student ID'], $family->getSalt()));
            } else {
                $family = $families[$row['Student ID']];
            }

            $adult = new User($row['Full Name']);
            $adult->setPhone($row['Cell Phone']);
            $family->addSpouse($adult);
            $em->persist($adult);

            $family->setTimeToLogin(new \DateTime());
            $family->setRoles(array('ROLE_USER'));
            $family->setEnabled(true);
            $em->persist($family);
            $families[$row['Student ID']] = $family;
        }
        $em->flush();
        return true;
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
