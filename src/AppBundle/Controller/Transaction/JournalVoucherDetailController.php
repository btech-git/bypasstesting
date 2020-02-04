<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\JournalVoucherDetail;
use AppBundle\Form\Transaction\JournalVoucherDetailType;
use AppBundle\Grid\Transaction\JournalVoucherDetailGridType;

/**
 * @Route("/transaction/journal_voucher_detail")
 */
class JournalVoucherDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_journal_voucher_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(JournalVoucherDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(JournalVoucherDetailGridType::class, $repository, $request);

        return $this->render('transaction/journal_voucher_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_journal_voucher_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/journal_voucher_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_journal_voucher_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $journalVoucherDetail = new JournalVoucherDetail();
        $form = $this->createForm(JournalVoucherDetailType::class, $journalVoucherDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(JournalVoucherDetail::class);
            $repository->add($journalVoucherDetail);

            return $this->redirectToRoute('transaction_journal_voucher_detail_show', array('id' => $journalVoucherDetail->getId()));
        }

        return $this->render('transaction/journal_voucher_detail/new.html.twig', array(
            'journalVoucherDetail' => $journalVoucherDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_journal_voucher_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(JournalVoucherDetail $journalVoucherDetail)
    {
        return $this->render('transaction/journal_voucher_detail/show.html.twig', array(
            'journalVoucherDetail' => $journalVoucherDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_journal_voucher_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, JournalVoucherDetail $journalVoucherDetail)
    {
        $form = $this->createForm(JournalVoucherDetailType::class, $journalVoucherDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(JournalVoucherDetail::class);
            $repository->update($journalVoucherDetail);

            return $this->redirectToRoute('transaction_journal_voucher_detail_show', array('id' => $journalVoucherDetail->getId()));
        }

        return $this->render('transaction/journal_voucher_detail/edit.html.twig', array(
            'journalVoucherDetail' => $journalVoucherDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_journal_voucher_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, JournalVoucherDetail $journalVoucherDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(JournalVoucherDetail::class);
                $repository->remove($journalVoucherDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_journal_voucher_detail_index');
        }

        return $this->render('transaction/journal_voucher_detail/delete.html.twig', array(
            'journalVoucherDetail' => $journalVoucherDetail,
            'form' => $form->createView(),
        ));
    }
}
