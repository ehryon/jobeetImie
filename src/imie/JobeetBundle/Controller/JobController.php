<?php

namespace imie\JobeetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use imie\JobeetBundle\Entity\Job;
use imie\JobeetBundle\Form\JobType;

/**
 * Job controller.
 *
 */
class JobController extends Controller {

    /**
     * return job categories
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('imieJobeetBundle:Category')->getWithJobs();

        foreach ($categories as $category) {
            $category->setActiveJobs($em->getRepository('imieJobeetBundle:Job')->getActiveJobs($category->getId(), $this->container->getParameter('max_jobs_on_homepage')));
            $category->setMoreJobs($em->getRepository('imieJobeetBundle:Job')->countActiveJobs($category->getId()) - $this->container->getParameter('max_jobs_on_homepage'));
        }

        return $this->render('imieJobeetBundle:Job:index.html.twig', array(
                    'categories' => $categories
        ));
    }

    /**
     * 
     * @return type
     */
    public function createAction() {
        $entity = new Job();
        $request = $this->getRequest();
        $form = $this->createForm(new JobType(), $entity);
        $form->bindRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('imie_job_show', array(
                                'company' => $entity->getCompanySlug(),
                                'location' => $entity->getLocationSlug(),
                                'id' => $entity->getId(),
                                'position' => $entity->getPositionSlug()
            )));
        }
        return $this->render('imieJobeetBundle:Job:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView()
        ));
    }

    /**
     * Creates a form to create a Job entity.
     *
     * @param Job $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Job $entity) {
        $form = $this->createForm(new JobType(), $entity, array(
            'action' => $this->generateUrl('imie_job_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Job entity.
     *
     */
    public function newAction() {
        $entity = new Job();
        $entity->setType('full-time');
        $form = $this->createForm(new JobType(), $entity);
        return $this->render('imieJobeetBundle:Job:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a Job entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('imieJobeetBundle:Job')->getActiveJob($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('imieJobeetBundle:Job:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Job entity.
     *
     */
    public function editAction($token) {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('imieJobeetBundle:Job')->findOneByToken($token);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }
        $editForm = $this->createForm(new JobType(), $entity);
        $deleteForm = $this->createDeleteForm($token);
        return $this->render('imieJobeetBundle:Job:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Job entity.
     *
     * @param Job $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Job $entity) {
        $form = $this->createForm(new JobType(), $entity, array(
            'action' => $this->generateUrl('imie_job_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Job entity.
     *
     */
    public function updateAction($token) {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('imieJobeetBundle:Job')->findOneByToken($token);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }
        $editForm = $this->createForm(new JobType(), $entity);
        $deleteForm = $this->createDeleteForm($token);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('imie_job_edit', array('token' => $token)));
        }
        return $this->render('imieJobeetBundle:Job:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Job entity.
     *
     */
    public function deleteAction($token) {
        $form = $this->createDeleteForm($token);
        $request = $this->getRequest();
        $form->bindRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('imieJobeetBundle:Job')->findOneByToken($token);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('imie_job'));
    }

    /**
     * Creates a form to delete a Job entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token) {
        return $this->createFormBuilder(array('token' => $token))
                        ->add('token', 'hidden')
                        ->getForm()
        ;
    }

    public function previewAction($token) {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('imieJobeetBundle:Job')->findOneByToken($token);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }
        $deleteForm = $this->createDeleteForm($entity->getId());
        return $this->render('imieJobeetBundle:Job:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

}
