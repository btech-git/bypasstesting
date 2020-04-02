<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;
use AppBundle\Form\Transaction\PurchaseDeliveryOrderType;
use AppBundle\Grid\Transaction\PurchaseDeliveryOrderGridType;
use AppBundle\Grid\Transaction\PurchaseDeliveryOrderOutstandingGridType;

/**
 * @Route("/transaction/purchase_delivery_order")
 */
class PurchaseDeliveryOrderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_delivery_order_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseDeliveryOrder::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseDeliveryOrderGridType::class, $repository, $request);

        return $this->render('transaction/purchase_delivery_order/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_delivery_order_index")
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_delivery_order/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_purchase_delivery_order_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $purchaseDeliveryOrder = new PurchaseDeliveryOrder();
        
        $purchaseDeliveryOrderService = $this->get('app.transaction.purchase_delivery_order_form');
        $form = $this->createForm(PurchaseDeliveryOrderType::class, $purchaseDeliveryOrder, array(
            'service' => $purchaseDeliveryOrderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseDeliveryOrderService->save($purchaseDeliveryOrder);

            return $this->redirectToRoute('transaction_purchase_delivery_order_show', array('id' => $purchaseDeliveryOrder->getId()));
        }

        return $this->render('transaction/purchase_delivery_order/new.'.$_format.'.twig', array(
            'purchaseDeliveryOrder' => $purchaseDeliveryOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_delivery_order_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function showAction(PurchaseDeliveryOrder $purchaseDeliveryOrder)
    {
        return $this->render('transaction/purchase_delivery_order/show.html.twig', array(
            'purchaseDeliveryOrder' => $purchaseDeliveryOrder,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_purchase_delivery_order_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function editAction(Request $request, PurchaseDeliveryOrder $purchaseDeliveryOrder, $_format = 'html')
    {
        $purchaseDeliveryOrderService = $this->get('app.transaction.purchase_delivery_order_form');
        $form = $this->createForm(PurchaseDeliveryOrderType::class, $purchaseDeliveryOrder, array(
            'service' => $purchaseDeliveryOrderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseDeliveryOrderService->save($purchaseDeliveryOrder);

            return $this->redirectToRoute('transaction_purchase_delivery_order_show', array('id' => $purchaseDeliveryOrder->getId()));
        }

        return $this->render('transaction/purchase_delivery_order/edit.html.twig', array(
            'purchaseDeliveryOrder' => $purchaseDeliveryOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_delivery_order_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function deleteAction(Request $request, PurchaseDeliveryOrder $purchaseDeliveryOrder)
    {
        $purchaseDeliveryOrderService = $this->get('app.transaction.purchase_delivery_order_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $purchaseDeliveryOrderService->delete($purchaseDeliveryOrder);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_delivery_order_index');
        }

        return $this->render('transaction/purchase_delivery_order/delete.html.twig', array(
            'purchaseDeliveryOrder' => $purchaseDeliveryOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/grid_outstanding", name="transaction_purchase_delivery_order_grid_outstanding", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function gridOutstandingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseDeliveryOrder::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseDeliveryOrderOutstandingGridType::class, $repository, $request);

        return $this->render('transaction/purchase_delivery_order/grid_outstanding.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/index_outstanding", name="transaction_purchase_delivery_order_index_outstanding")
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function indexOutstandingAction()
    {
        return $this->render('transaction/purchase_delivery_order/index_outstanding.html.twig');
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_purchase_delivery_order_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function memoAction(PurchaseDeliveryOrder $purchaseDeliveryOrder)
    {
        return $this->render('transaction/purchase_delivery_order/memo.html.twig', array(
            'purchaseDeliveryOrder' => $purchaseDeliveryOrder,
        ));
    }
    
    /**
     * @Route("/{id}/reset_sale_order", name="transaction_purchase_delivery_order_reset_sale_order", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function resetSaleOrderAction(Request $request, PurchaseDeliveryOrder $purchaseDeliveryOrder)
    {
        $purchaseDeliveryOrderService = $this->get('app.transaction.purchase_delivery_order_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $saleOrder = $purchaseDeliveryOrder->getSaleOrder();
            if ($form->isValid()) {
                $purchaseDeliveryOrderService->resetSaleOrder($purchaseDeliveryOrder);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was reset successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reset the record.'));
            }

            return $this->redirectToRoute('transaction_sale_order_show', array('id' => $saleOrder->getId()));
        }

        return $this->render('transaction/purchase_delivery_order/reset_sale_order.html.twig', array(
            'purchaseDeliveryOrder' => $purchaseDeliveryOrder,
            'form' => $form->createView(),
        ));
    }
}
