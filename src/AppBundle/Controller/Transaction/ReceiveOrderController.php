<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ReceiveOrder;
use AppBundle\Form\Transaction\ReceiveOrderType;
use AppBundle\Grid\Transaction\ReceiveOrderGridType;

/**
 * @Route("/transaction/receive_order")
 */
class ReceiveOrderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_receive_order_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ReceiveOrder::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ReceiveOrderGridType::class, $repository, $request);

        return $this->render('transaction/receive_order/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_receive_order_index")
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/receive_order/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_receive_order_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $receiveOrder = new ReceiveOrder();

        $receiveOrderService = $this->get('app.transaction.receive_order_form');
        $form = $this->createForm(ReceiveOrderType::class, $receiveOrder, array(
            'service' => $receiveOrderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $receiveOrderService->save($receiveOrder);

            return $this->redirectToRoute('transaction_receive_order_show', array('id' => $receiveOrder->getId()));
        }

        return $this->render('transaction/receive_order/new.'.$_format.'.twig', array(
            'receiveOrder' => $receiveOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_receive_order_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function showAction(ReceiveOrder $receiveOrder)
    {
        return $this->render('transaction/receive_order/show.html.twig', array(
            'receiveOrder' => $receiveOrder,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_receive_order_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, ReceiveOrder $receiveOrder, $_format = 'html')
    {
        $receiveOrderService = $this->get('app.transaction.receive_order_form');
        $form = $this->createForm(ReceiveOrderType::class, $receiveOrder, array(
            'service' => $receiveOrderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $receiveOrderService->save($receiveOrder);

            return $this->redirectToRoute('transaction_receive_order_show', array('id' => $receiveOrder->getId()));
        }

        return $this->render('transaction/receive_order/edit.'.$_format.'.twig', array(
            'receiveOrder' => $receiveOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_receive_order_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, ReceiveOrder $receiveOrder)
    {
        $receiveOrderService = $this->get('app.transaction.receive_order_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $receiveOrderService->delete($receiveOrder);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_receive_order_index');
        }

        return $this->render('transaction/receive_order/delete.html.twig', array(
            'receiveOrder' => $receiveOrder,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_receive_order_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function memoAction(ReceiveOrder $receiveOrder)
    {
        return $this->render('transaction/receive_order/memo.html.twig', array(
            'receiveOrder' => $receiveOrder,
        ));
    }
}
