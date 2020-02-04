<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\DepositDetail;
use AppBundle\Form\Transaction\DepositDetailType;
use AppBundle\Grid\Transaction\DepositDetailGridType;

/**
 * @Route("/transaction/deposit_detail")
 */
class DepositDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_deposit_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(DepositDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DepositDetailGridType::class, $repository, $request);

        return $this->render('transaction/deposit_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_deposit_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/deposit_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_deposit_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $depositDetail = new DepositDetail();
        $form = $this->createForm(DepositDetailType::class, $depositDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(DepositDetail::class);
            $repository->add($depositDetail);

            return $this->redirectToRoute('transaction_deposit_detail_show', array('id' => $depositDetail->getId()));
        }

        return $this->render('transaction/deposit_detail/new.html.twig', array(
            'depositDetail' => $depositDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_deposit_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(DepositDetail $depositDetail)
    {
        return $this->render('transaction/deposit_detail/show.html.twig', array(
            'depositDetail' => $depositDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_deposit_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, DepositDetail $depositDetail)
    {
        $form = $this->createForm(DepositDetailType::class, $depositDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(DepositDetail::class);
            $repository->update($depositDetail);

            return $this->redirectToRoute('transaction_deposit_detail_show', array('id' => $depositDetail->getId()));
        }

        return $this->render('transaction/deposit_detail/edit.html.twig', array(
            'depositDetail' => $depositDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_deposit_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, DepositDetail $depositDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(DepositDetail::class);
                $repository->remove($depositDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_deposit_detail_index');
        }

        return $this->render('transaction/deposit_detail/delete.html.twig', array(
            'depositDetail' => $depositDetail,
            'form' => $form->createView(),
        ));
    }
}
