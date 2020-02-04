<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleOrder;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;
use AppBundle\Form\Transaction\SaleOrderType;
use AppBundle\Form\Transaction\SaleOrderStockHeaderType;
use AppBundle\Grid\Transaction\SaleOrderGridType;
use AppBundle\Grid\Transaction\SaleOrderApprovalGridType;

/**
 * @Route("/transaction/sale_order")
 */
class SaleOrderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_order_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleOrder::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleOrderGridType::class, $repository, $request);

        return $this->render('transaction/sale_order/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_order_index")
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_order/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_order_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $saleOrder = new SaleOrder();

        $saleOrderService = $this->get('app.transaction.sale_order_form');
        $form = $this->createForm(SaleOrderType::class, $saleOrder, array(
            'service' => $saleOrderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleOrderService->save($saleOrder);

            return $this->redirectToRoute('transaction_sale_order_show', array('id' => $saleOrder->getId()));
        }

        return $this->render('transaction/sale_order/new.'.$_format.'.twig', array(
            'saleOrder' => $saleOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_order_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function showAction(SaleOrder $saleOrder)
    {
        $saleOrderStockService = $this->get('app.transaction.sale_order_stock_form');
        $stockValid = $saleOrderStockService->isValidForStockReferring($saleOrder);
        
        return $this->render('transaction/sale_order/show.html.twig', array(
            'saleOrder' => $saleOrder,
            'stockValid' => $stockValid,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_sale_order_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function editAction(Request $request, SaleOrder $saleOrder, $_format = 'html')
    {
        $saleOrderService = $this->get('app.transaction.sale_order_form');
        $form = $this->createForm(SaleOrderType::class, $saleOrder, array(
            'service' => $saleOrderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleOrderService->save($saleOrder);

            return $this->redirectToRoute('transaction_sale_order_show', array('id' => $saleOrder->getId()));
        }

        return $this->render('transaction/sale_order/edit.'.$_format.'.twig', array(
            'saleOrder' => $saleOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_order_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function deleteAction(Request $request, SaleOrder $saleOrder)
    {
        $saleOrderService = $this->get('app.transaction.sale_order_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleOrderService->delete($saleOrder);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_order_index');
        }

        return $this->render('transaction/sale_order/delete.html.twig', array(
            'saleOrder' => $saleOrder,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_sale_order_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function memoAction(SaleOrder $saleOrder)
    {
        return $this->render('transaction/sale_order/memo.html.twig', array(
            'saleOrder' => $saleOrder,
        ));
    }
    
    /**
     * @Route("/{id}/stock_referring", name="transaction_sale_order_stock_referring", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function stockReferringAction(Request $request, SaleOrder $saleOrder)
    {
        $saleOrderStockService = $this->get('app.transaction.sale_order_stock_form');
        if (!$saleOrderStockService->isValidForStockReferring($saleOrder)) {
            return $this->redirectToRoute('transaction_sale_order_index');
        }
        $form = $this->createForm(SaleOrderStockHeaderType::class, $saleOrder, array(
            'service' => $saleOrderStockService,
            'purchaseDeliveryOrderRepository' => $this->getDoctrine()->getManager()->getRepository(PurchaseDeliveryOrder::class),
            'vehicleModel' => $saleOrder->getVehicleModel(),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saleOrderStockService->save($saleOrder);

            return $this->redirectToRoute('transaction_sale_order_show', array('id' => $saleOrder->getId()));
        }

        return $this->render('transaction/sale_order/stock_referring.html.twig', array(
            'saleOrder' => $saleOrder,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/grid_approval", name="transaction_sale_order_grid_approval", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function gridApprovalAction(Request $request)
    {
        $options = array();
        $options['user'] = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleOrder::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleOrderApprovalGridType::class, $repository, $request, $options);

        return $this->render('transaction/sale_order/grid_approval.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/approval", name="transaction_sale_order_approval")
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function approvalAction()
    {
        return $this->render('transaction/sale_order/approval.html.twig');
    }
    /**
     * @Route("/{id}/approve", name="transaction_sale_order_approve", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function approveAction(Request $request, SaleOrder $saleOrder)
    {
        $saleOrderService = $this->get('app.transaction.sale_order_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleOrderService->approve($saleOrder, $this->getUser());

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'Transaction was successfully approved.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the transaction.'));
            }

            return $this->redirectToRoute('transaction_sale_order_approval');
        }
        
        return $this->render('transaction/sale_order/approve.html.twig', array(
            'saleOrder' => $saleOrder,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/reject", name="transaction_sale_order_reject", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function rejectAction(Request $request, SaleOrder $saleOrder)
    {
        $saleOrderService = $this->get('app.transaction.sale_order_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleOrderService->reject($saleOrder, $this->getUser());

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'Transaction was successfully rejected.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the transaction.'));
            }

            return $this->redirectToRoute('transaction_sale_order_approval');
        }
        
        return $this->render('transaction/sale_order/reject.html.twig', array(
            'saleOrder' => $saleOrder,
            'form' => $form->createView(),
        ));
    }
}
