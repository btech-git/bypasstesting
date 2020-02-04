<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Form\Transaction\PurchaseInvoiceHeaderGeneralType;
use AppBundle\Grid\Transaction\PurchaseInvoiceHeaderGeneralGridType;

/**
 * @Route("/transaction/purchase_invoice_header_general")
 */
class PurchaseInvoiceHeaderGeneralController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_invoice_header_general_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseInvoiceHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseInvoiceHeaderGeneralGridType::class, $repository, $request);

        return $this->render('transaction/purchase_invoice_header_general/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_invoice_header_general_index")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_invoice_header_general/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_purchase_invoice_header_general_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $purchaseInvoiceHeader = new PurchaseInvoiceHeader();
        
        $purchaseInvoiceHeaderGeneralService = $this->get('app.transaction.purchase_invoice_header_general_form');
        $form = $this->createForm(PurchaseInvoiceHeaderGeneralType::class, $purchaseInvoiceHeader, array(
            'service' => $purchaseInvoiceHeaderGeneralService,
            'init' => array('date' => date('Y-m-d'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderGeneralService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('transaction_purchase_invoice_header_general_show', array('id' => $purchaseInvoiceHeader->getId()));
        }

        return $this->render('transaction/purchase_invoice_header_general/new.'.$_format.'.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
            'purchaseInvoiceDetailGeneralsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_invoice_header_general_show")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function showAction(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        return $this->render('transaction/purchase_invoice_header_general/show.html.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_purchase_invoice_header_general_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, $_format = 'html')
    {
        $purchaseInvoiceDetailGeneralsCount = $purchaseInvoiceHeader->getPurchaseInvoiceDetailGenerals()->count();

        $purchaseInvoiceHeaderGeneralService = $this->get('app.transaction.purchase_invoice_header_general_form');
        $form = $this->createForm(PurchaseInvoiceHeaderGeneralType::class, $purchaseInvoiceHeader, array(
            'service' => $purchaseInvoiceHeaderGeneralService,
            'init' => array('date' => date('Y-m-d'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderGeneralService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('transaction_purchase_invoice_header_general_show', array('id' => $purchaseInvoiceHeader->getId()));
        }

        return $this->render('transaction/purchase_invoice_header_general/edit.'.$_format.'.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
            'purchaseInvoiceDetailGeneralsCount' => $purchaseInvoiceDetailGeneralsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_invoice_header_general_delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeaderGeneralService = $this->get('app.transaction.purchase_invoice_header_general_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $purchaseInvoiceHeaderGeneralService->delete($purchaseInvoiceHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_invoice_header_general_index');
        }

        return $this->render('transaction/purchase_invoice_header_general/delete.html.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_purchase_invoice_header_general_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function memoAction(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        return $this->render('transaction/purchase_invoice_header_general/memo.html.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
        ));
    }
}
