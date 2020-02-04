<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\Employee;
use AppBundle\Form\Master\EmployeeType;
use AppBundle\Grid\Master\EmployeeGridType;

/**
 * @Route("/master/employee")
 */
class EmployeeController extends Controller
{
    /**
     * @Route("/grid", name="master_employee_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Employee::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(EmployeeGridType::class, $repository, $request);

        return $this->render('master/employee/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_employee_index")
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function indexAction()
    {
        return $this->render('master/employee/index.html.twig');
    }

    /**
     * @Route("/new", name="master_employee_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function newAction(Request $request)
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Employee::class);
            $repository->add($employee);

            return $this->redirectToRoute('master_employee_show', array('id' => $employee->getId()));
        }

        return $this->render('master/employee/new.html.twig', array(
            'employee' => $employee,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_employee_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function showAction(Employee $employee)
    {
        return $this->render('master/employee/show.html.twig', array(
            'employee' => $employee,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_employee_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function editAction(Request $request, Employee $employee)
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Employee::class);
            $repository->update($employee);

            return $this->redirectToRoute('master_employee_show', array('id' => $employee->getId()));
        }

        return $this->render('master/employee/edit.html.twig', array(
            'employee' => $employee,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_employee_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function deleteAction(Request $request, Employee $employee)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(Employee::class);
                $repository->remove($employee);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_employee_index');
        }

        return $this->render('master/employee/delete.html.twig', array(
            'employee' => $employee,
            'form' => $form->createView(),
        ));
    }
}
