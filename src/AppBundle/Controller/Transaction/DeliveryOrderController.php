<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\DeliveryOrder;
use AppBundle\Form\Transaction\DeliveryOrderType;
use AppBundle\Grid\Transaction\DeliveryOrderGridType;

/**
 * @Route("/transaction/delivery_order")
 */
class DeliveryOrderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_delivery_order_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(DeliveryOrder::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DeliveryOrderGridType::class, $repository, $request);

        return $this->render('transaction/delivery_order/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_delivery_order_index")
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/delivery_order/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_delivery_order_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $deliveryOrder = new DeliveryOrder();
        
        $deliveryOrderService = $this->get('app.transaction.delivery_order_form');
        $form = $this->createForm(DeliveryOrderType::class, $deliveryOrder, array(
            'service' => $deliveryOrderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $deliveryOrderService->save($deliveryOrder);

            return $this->redirectToRoute('transaction_delivery_order_show', array('id' => $deliveryOrder->getId()));
        }

        return $this->render('transaction/delivery_order/new.'.$_format.'.twig', array(
            'deliveryOrder' => $deliveryOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_delivery_order_show", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function showAction(Request $request, DeliveryOrder $deliveryOrder)
    {
        $deliveryOrderService = $this->get('app.transaction.delivery_order_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $deliveryOrder->setStaffApproval($this->getUser());
                $deliveryOrderService->save($deliveryOrder);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was updated successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to update the record.'));
            }
        }

        return $this->render('transaction/delivery_order/show.html.twig', array(
            'deliveryOrder' => $deliveryOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_delivery_order_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function editAction(Request $request, DeliveryOrder $deliveryOrder, $_format = 'html')
    {
        $deliveryOrderService = $this->get('app.transaction.delivery_order_form');
        $form = $this->createForm(DeliveryOrderType::class, $deliveryOrder, array(
            'service' => $deliveryOrderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $deliveryOrderService->save($deliveryOrder);

            return $this->redirectToRoute('transaction_delivery_order_show', array('id' => $deliveryOrder->getId()));
        }

        return $this->render('transaction/delivery_order/edit.'.$_format.'.twig', array(
            'deliveryOrder' => $deliveryOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_delivery_order_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function deleteAction(Request $request, DeliveryOrder $deliveryOrder)
    {
        $deliveryOrderService = $this->get('app.transaction.delivery_order_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $deliveryOrderService->delete($deliveryOrder);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_delivery_order_index');
        }

        return $this->render('transaction/delivery_order/delete.html.twig', array(
            'deliveryOrder' => $deliveryOrder,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_delivery_order_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function memoAction(DeliveryOrder $deliveryOrder)
    {
        return $this->render('transaction/delivery_order/memo.html.twig', array(
            'deliveryOrder' => $deliveryOrder,
        ));
    }
    
    /**
     * @Route("/{id}/memo_hand_over", name="transaction_delivery_order_memo_hand_over", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function memoHandOverAction(DeliveryOrder $deliveryOrder)
    {
        return $this->render('transaction/delivery_order/memo_hand_over.html.twig', array(
            'deliveryOrder' => $deliveryOrder,
        ));
    }    
}
