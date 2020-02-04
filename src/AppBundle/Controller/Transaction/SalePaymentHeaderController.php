<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SalePaymentHeader;
use AppBundle\Form\Transaction\SalePaymentHeaderType;
use AppBundle\Grid\Transaction\SalePaymentHeaderGridType;

/**
 * @Route("/transaction/sale_payment_header")
 */
class SalePaymentHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_payment_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_FINANCE_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SalePaymentHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SalePaymentHeaderGridType::class, $repository, $request);

        return $this->render('transaction/sale_payment_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_payment_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_payment_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_payment_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_FINANCE_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $salePaymentHeader = new SalePaymentHeader();

        $salePaymentHeaderService = $this->get('app.transaction.sale_payment_header_form');
        $form = $this->createForm(SalePaymentHeaderType::class, $salePaymentHeader, array(
            'service' => $salePaymentHeaderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $salePaymentHeaderService->save($salePaymentHeader);

            return $this->redirectToRoute('transaction_sale_payment_header_show', array('id' => $salePaymentHeader->getId()));
        }

        return $this->render('transaction/sale_payment_header/new.'.$_format.'.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form->createView(),
            'salePaymentDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_payment_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF')")
     */
    public function showAction(SalePaymentHeader $salePaymentHeader)
    {
        return $this->render('transaction/sale_payment_header/show.html.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_sale_payment_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, SalePaymentHeader $salePaymentHeader, $_format = 'html')
    {
        $salePaymentDetailsCount = $salePaymentHeader->getSalePaymentDetails()->count();

        $salePaymentHeaderService = $this->get('app.transaction.sale_payment_header_form');
        $form = $this->createForm(SalePaymentHeaderType::class, $salePaymentHeader, array(
            'service' => $salePaymentHeaderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $salePaymentHeaderService->save($salePaymentHeader);

            return $this->redirectToRoute('transaction_sale_payment_header_show', array('id' => $salePaymentHeader->getId()));
        }

        return $this->render('transaction/sale_payment_header/edit.'.$_format.'.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form->createView(),
            'salePaymentDetailsCount' => $salePaymentDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_payment_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, SalePaymentHeader $salePaymentHeader)
    {
        $salePaymentHeaderService = $this->get('app.transaction.sale_payment_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $salePaymentHeaderService->delete($salePaymentHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_payment_header_index');
        }

        return $this->render('transaction/sale_payment_header/delete.html.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_sale_payment_header_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_FINANCE_STAFF')")
     */
    public function memoAction(SalePaymentHeader $salePaymentHeader)
    {
        return $this->render('transaction/sale_payment_header/memo.html.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
        ));
    }
}
