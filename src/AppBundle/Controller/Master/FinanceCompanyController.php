<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\FinanceCompany;
use AppBundle\Form\Master\FinanceCompanyType;
use AppBundle\Grid\Master\FinanceCompanyGridType;

/**
 * @Route("/master/finance_company")
 */
class FinanceCompanyController extends Controller
{
    /**
     * @Route("/grid", name="master_finance_company_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(FinanceCompany::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(FinanceCompanyGridType::class, $repository, $request);

        return $this->render('master/finance_company/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_finance_company_index")
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function indexAction()
    {
        return $this->render('master/finance_company/index.html.twig');
    }

    /**
     * @Route("/new", name="master_finance_company_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function newAction(Request $request)
    {
        $financeCompany = new FinanceCompany();
        $form = $this->createForm(FinanceCompanyType::class, $financeCompany);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(FinanceCompany::class);
            $repository->add($financeCompany);

            return $this->redirectToRoute('master_finance_company_show', array('id' => $financeCompany->getId()));
        }

        return $this->render('master/finance_company/new.html.twig', array(
            'financeCompany' => $financeCompany,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_finance_company_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function showAction(FinanceCompany $financeCompany)
    {
        return $this->render('master/finance_company/show.html.twig', array(
            'financeCompany' => $financeCompany,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_finance_company_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function editAction(Request $request, FinanceCompany $financeCompany)
    {
        $form = $this->createForm(FinanceCompanyType::class, $financeCompany);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(FinanceCompany::class);
            $repository->update($financeCompany);

            return $this->redirectToRoute('master_finance_company_show', array('id' => $financeCompany->getId()));
        }

        return $this->render('master/finance_company/edit.html.twig', array(
            'financeCompany' => $financeCompany,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_finance_company_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function deleteAction(Request $request, FinanceCompany $financeCompany)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(FinanceCompany::class);
                $repository->remove($financeCompany);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_finance_company_index');
        }

        return $this->render('master/finance_company/delete.html.twig', array(
            'financeCompany' => $financeCompany,
            'form' => $form->createView(),
        ));
    }
}
