<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ExpenseDetail;
use AppBundle\Form\Transaction\ExpenseDetailType;
use AppBundle\Grid\Transaction\ExpenseDetailGridType;

/**
 * @Route("/transaction/expense_detail")
 */
class ExpenseDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_expense_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ExpenseDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ExpenseDetailGridType::class, $repository, $request);

        return $this->render('transaction/expense_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_expense_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/expense_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_expense_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $expenseDetail = new ExpenseDetail();
        $form = $this->createForm(ExpenseDetailType::class, $expenseDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(ExpenseDetail::class);
            $repository->add($expenseDetail);

            return $this->redirectToRoute('transaction_expense_detail_show', array('id' => $expenseDetail->getId()));
        }

        return $this->render('transaction/expense_detail/new.html.twig', array(
            'expenseDetail' => $expenseDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_expense_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(ExpenseDetail $expenseDetail)
    {
        return $this->render('transaction/expense_detail/show.html.twig', array(
            'expenseDetail' => $expenseDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_expense_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, ExpenseDetail $expenseDetail)
    {
        $form = $this->createForm(ExpenseDetailType::class, $expenseDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(ExpenseDetail::class);
            $repository->update($expenseDetail);

            return $this->redirectToRoute('transaction_expense_detail_show', array('id' => $expenseDetail->getId()));
        }

        return $this->render('transaction/expense_detail/edit.html.twig', array(
            'expenseDetail' => $expenseDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_expense_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, ExpenseDetail $expenseDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(ExpenseDetail::class);
                $repository->remove($expenseDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_expense_detail_index');
        }

        return $this->render('transaction/expense_detail/delete.html.twig', array(
            'expenseDetail' => $expenseDetail,
            'form' => $form->createView(),
        ));
    }
}
