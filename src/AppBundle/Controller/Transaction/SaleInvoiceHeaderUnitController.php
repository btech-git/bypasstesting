<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Form\Transaction\SaleInvoiceHeaderUnitType;
use AppBundle\Grid\Transaction\SaleInvoiceHeaderUnitGridType;

/**
 * @Route("/transaction/sale_invoice_header_unit")
 */
class SaleInvoiceHeaderUnitController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_invoice_header_unit_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceHeaderUnitGridType::class, $repository, $request);

        return $this->render('transaction/sale_invoice_header_unit/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_invoice_header_unit_index")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_invoice_header_unit/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_invoice_header_unit_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $saleInvoiceHeader = new SaleInvoiceHeader();
        
        $saleInvoiceHeaderUnitService = $this->get('app.transaction.sale_invoice_header_unit_form');
        $form = $this->createForm(SaleInvoiceHeaderUnitType::class, $saleInvoiceHeader, array(
            'service' => $saleInvoiceHeaderUnitService,
            'init' => array('date' => date('Y-m-d'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderUnitService->save($saleInvoiceHeader);

            return $this->redirectToRoute('transaction_sale_invoice_header_unit_show', array('id' => $saleInvoiceHeader->getId()));
        }

        return $this->render('transaction/sale_invoice_header_unit/new.'.$_format.'.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
            'saleInvoiceDetailUnitsCount' => 0,
            'saleInvoiceDetailUnitDownpaymentsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_invoice_header_unit_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function showAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/sale_invoice_header_unit/show.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_invoice_header_unit_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, SaleInvoiceHeader $saleInvoiceHeader, $_format = 'html')
    {
        $saleInvoiceDetailUnitsCount = $saleInvoiceHeader->getSaleInvoiceDetailUnits()->count();
        $saleInvoiceDetailUnitDownpaymentsCount = $saleInvoiceHeader->getSaleInvoiceDetailUnitDownpayments()->count();

        $saleInvoiceHeaderUnitService = $this->get('app.transaction.sale_invoice_header_unit_form');
        $form = $this->createForm(SaleInvoiceHeaderUnitType::class, $saleInvoiceHeader, array(
            'service' => $saleInvoiceHeaderUnitService,
            'init' => array('date' => date('Y-m-d'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderUnitService->save($saleInvoiceHeader);

            return $this->redirectToRoute('transaction_sale_invoice_header_unit_show', array('id' => $saleInvoiceHeader->getId()));
        }

        return $this->render('transaction/sale_invoice_header_unit/edit.'.$_format.'.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
            'saleInvoiceDetailUnitsCount' => $saleInvoiceDetailUnitsCount,
            'saleInvoiceDetailUnitDownpaymentsCount' => $saleInvoiceDetailUnitDownpaymentsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_invoice_header_unit_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeaderUnitService = $this->get('app.transaction.sale_invoice_header_unit_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleInvoiceHeaderUnitService->delete($saleInvoiceHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_invoice_header_unit_index');
        }

        return $this->render('transaction/sale_invoice_header_unit/delete.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_sale_invoice_header_unit_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function memoAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/sale_invoice_header_unit/memo_plain.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }
}
