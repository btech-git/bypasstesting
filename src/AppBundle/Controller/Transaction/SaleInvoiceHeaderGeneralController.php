<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Form\Transaction\SaleInvoiceHeaderGeneralType;
use AppBundle\Grid\Transaction\SaleInvoiceHeaderGeneralGridType;

/**
 * @Route("/transaction/sale_invoice_header_general")
 */
class SaleInvoiceHeaderGeneralController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_invoice_header_general_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceHeaderGeneralGridType::class, $repository, $request);

        return $this->render('transaction/sale_invoice_header_general/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_invoice_header_general_index")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_invoice_header_general/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_invoice_header_general_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $saleInvoiceHeader = new SaleInvoiceHeader();
        
        $saleInvoiceHeaderGeneralService = $this->get('app.transaction.sale_invoice_header_general_form');
        $form = $this->createForm(SaleInvoiceHeaderGeneralType::class, $saleInvoiceHeader, array(
            'service' => $saleInvoiceHeaderGeneralService,
            'init' => array('date' => date('Y-m-d'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderGeneralService->save($saleInvoiceHeader);

            return $this->redirectToRoute('transaction_sale_invoice_header_general_show', array('id' => $saleInvoiceHeader->getId()));
        }

        return $this->render('transaction/sale_invoice_header_general/new.'.$_format.'.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
            'saleInvoiceDetailGeneralsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_invoice_header_general_show")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function showAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/sale_invoice_header_general/show.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_sale_invoice_header_general_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, SaleInvoiceHeader $saleInvoiceHeader, $_format = 'html')
    {
        $saleInvoiceDetailGeneralsCount = $saleInvoiceHeader->getSaleInvoiceDetailGenerals()->count();

        $saleInvoiceHeaderGeneralService = $this->get('app.transaction.sale_invoice_header_general_form');
        $form = $this->createForm(SaleInvoiceHeaderGeneralType::class, $saleInvoiceHeader, array(
            'service' => $saleInvoiceHeaderGeneralService,
            'init' => array('date' => date('Y-m-d'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderGeneralService->save($saleInvoiceHeader);

            return $this->redirectToRoute('transaction_sale_invoice_header_general_show', array('id' => $saleInvoiceHeader->getId()));
        }

        return $this->render('transaction/sale_invoice_header_general/edit.'.$_format.'.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
            'saleInvoiceDetailGeneralsCount' => $saleInvoiceDetailGeneralsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_invoice_header_general_delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeaderGeneralService = $this->get('app.transaction.sale_invoice_header_general_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleInvoiceHeaderGeneralService->delete($saleInvoiceHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_invoice_header_general_index');
        }

        return $this->render('transaction/sale_invoice_header_general/delete.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_sale_invoice_header_general_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF') or has_role('ROLE_CASHIER_STAFF')")
     */
    public function memoAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/sale_invoice_header_general/memo_plain.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }
}
