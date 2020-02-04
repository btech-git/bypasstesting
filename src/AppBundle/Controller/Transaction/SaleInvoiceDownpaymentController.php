<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;
use AppBundle\Form\Transaction\SaleInvoiceDownpaymentType;
use AppBundle\Grid\Transaction\SaleInvoiceDownpaymentGridType;

/**
 * @Route("/transaction/sale_invoice_downpayment")
 */
class SaleInvoiceDownpaymentController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_invoice_downpayment_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceDownpayment::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceDownpaymentGridType::class, $repository, $request);

        return $this->render('transaction/sale_invoice_downpayment/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_invoice_downpayment_index")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_invoice_downpayment/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_invoice_downpayment_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $saleInvoiceDownpayment = new SaleInvoiceDownpayment();
        
        $saleInvoiceDownpaymentService = $this->get('app.transaction.sale_invoice_downpayment_form');
        $form = $this->createForm(SaleInvoiceDownpaymentType::class, $saleInvoiceDownpayment, array(
            'service' => $saleInvoiceDownpaymentService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceDownpaymentService->save($saleInvoiceDownpayment);

            return $this->redirectToRoute('transaction_sale_invoice_downpayment_show', array('id' => $saleInvoiceDownpayment->getId()));
        }

        return $this->render('transaction/sale_invoice_downpayment/new.'.$_format.'.twig', array(
            'saleInvoiceDownpayment' => $saleInvoiceDownpayment,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_invoice_downpayment_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function showAction(SaleInvoiceDownpayment $saleInvoiceDownpayment)
    {
        return $this->render('transaction/sale_invoice_downpayment/show.html.twig', array(
            'saleInvoiceDownpayment' => $saleInvoiceDownpayment,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_invoice_downpayment_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, SaleInvoiceDownpayment $saleInvoiceDownpayment, $_format = 'html')
    {
        $saleInvoiceDownpaymentService = $this->get('app.transaction.sale_invoice_downpayment_form');
        $form = $this->createForm(SaleInvoiceDownpaymentType::class, $saleInvoiceDownpayment, array(
            'service' => $saleInvoiceDownpaymentService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceDownpaymentService->save($saleInvoiceDownpayment);

            return $this->redirectToRoute('transaction_sale_invoice_downpayment_show', array('id' => $saleInvoiceDownpayment->getId()));
        }

        return $this->render('transaction/sale_invoice_downpayment/edit.'.$_format.'.twig', array(
            'saleInvoiceDownpayment' => $saleInvoiceDownpayment,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_invoice_downpayment_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, SaleInvoiceDownpayment $saleInvoiceDownpayment)
    {
        $saleInvoiceDownpaymentService = $this->get('app.transaction.sale_invoice_downpayment_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleInvoiceDownpaymentService->delete($saleInvoiceDownpayment);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_invoice_downpayment_index');
        }

        return $this->render('transaction/sale_invoice_downpayment/delete.html.twig', array(
            'saleInvoiceDownpayment' => $saleInvoiceDownpayment,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_sale_invoice_downpayment_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function memoAction(SaleInvoiceDownpayment $saleInvoiceDownpayment)
    {
        return $this->render('transaction/sale_invoice_downpayment/memo_plain.html.twig', array(
            'saleInvoiceDownpayment' => $saleInvoiceDownpayment,
        ));
    }
}
