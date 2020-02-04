<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Form\Transaction\PurchaseInvoiceHeaderUnitType;
use AppBundle\Grid\Transaction\PurchaseInvoiceHeaderUnitGridType;

/**
 * @Route("/transaction/purchase_invoice_header_unit")
 */
class PurchaseInvoiceHeaderUnitController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_invoice_header_unit_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseInvoiceHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseInvoiceHeaderUnitGridType::class, $repository, $request);

        return $this->render('transaction/purchase_invoice_header_unit/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_invoice_header_unit_index")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_invoice_header_unit/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_purchase_invoice_header_unit_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $purchaseInvoiceHeader = new PurchaseInvoiceHeader();
        
        $purchaseInvoiceHeaderUnitService = $this->get('app.transaction.purchase_invoice_header_unit_form');
        $form = $this->createForm(PurchaseInvoiceHeaderUnitType::class, $purchaseInvoiceHeader, array(
            'service' => $purchaseInvoiceHeaderUnitService,
            'init' => array('date' => date('Y-m-d'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderUnitService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('transaction_purchase_invoice_header_unit_show', array('id' => $purchaseInvoiceHeader->getId()));
        }

        return $this->render('transaction/purchase_invoice_header_unit/new.'.$_format.'.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
            'purchaseInvoiceDetailUnitsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_invoice_header_unit_show")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF') or has_role('ROLE_PURCHASE_STAFF')")
     */
    public function showAction(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        return $this->render('transaction/purchase_invoice_header_unit/show.html.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_purchase_invoice_header_unit_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, $_format = 'html')
    {
        $purchaseInvoiceDetailUnitsCount = $purchaseInvoiceHeader->getPurchaseInvoiceDetailUnits()->count();

        $purchaseInvoiceHeaderUnitService = $this->get('app.transaction.purchase_invoice_header_unit_form');
        $form = $this->createForm(PurchaseInvoiceHeaderUnitType::class, $purchaseInvoiceHeader, array(
            'service' => $purchaseInvoiceHeaderUnitService,
            'init' => array('date' => date('Y-m-d'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderUnitService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('transaction_purchase_invoice_header_unit_show', array('id' => $purchaseInvoiceHeader->getId()));
        }

        return $this->render('transaction/purchase_invoice_header_unit/edit.'.$_format.'.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
            'purchaseInvoiceDetailUnitsCount' => $purchaseInvoiceDetailUnitsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_invoice_header_unit_delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeaderUnitService = $this->get('app.transaction.purchase_invoice_header_unit_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $purchaseInvoiceHeaderUnitService->delete($purchaseInvoiceHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_invoice_header_unit_index');
        }

        return $this->render('transaction/purchase_invoice_header_unit/delete.html.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
        ));
    }
}
