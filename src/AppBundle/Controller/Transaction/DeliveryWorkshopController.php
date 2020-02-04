<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\DeliveryWorkshop;
use AppBundle\Form\Transaction\DeliveryWorkshopType;
use AppBundle\Grid\Transaction\DeliveryWorkshopGridType;

/**
 * @Route("/transaction/delivery_workshop")
 */
class DeliveryWorkshopController extends Controller
{
    /**
     * @Route("/grid", name="transaction_delivery_workshop_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(DeliveryWorkshop::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DeliveryWorkshopGridType::class, $repository, $request);

        return $this->render('transaction/delivery_workshop/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_delivery_workshop_index")
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/delivery_workshop/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_delivery_workshop_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $deliveryWorkshop = new DeliveryWorkshop();
        
        $deliveryWorkshopService = $this->get('app.transaction.delivery_workshop_form');
        $form = $this->createForm(DeliveryWorkshopType::class, $deliveryWorkshop, array(
            'service' => $deliveryWorkshopService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $deliveryWorkshopService->save($deliveryWorkshop);

            return $this->redirectToRoute('transaction_delivery_workshop_show', array('id' => $deliveryWorkshop->getId()));
        }

        return $this->render('transaction/delivery_workshop/new.'.$_format.'.twig', array(
            'deliveryWorkshop' => $deliveryWorkshop,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_delivery_workshop_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function showAction(DeliveryWorkshop $deliveryWorkshop)
    {
        return $this->render('transaction/delivery_workshop/show.html.twig', array(
            'deliveryWorkshop' => $deliveryWorkshop,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_delivery_workshop_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function editAction(Request $request, DeliveryWorkshop $deliveryWorkshop, $_format = 'html')
    {
        $deliveryWorkshopService = $this->get('app.transaction.delivery_workshop_form');
        $form = $this->createForm(DeliveryWorkshopType::class, $deliveryWorkshop, array(
            'service' => $deliveryWorkshopService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $deliveryWorkshopService->save($deliveryWorkshop);

            return $this->redirectToRoute('transaction_delivery_workshop_show', array('id' => $deliveryWorkshop->getId()));
        }

        return $this->render('transaction/delivery_workshop/edit.'.$_format.'.twig', array(
            'deliveryWorkshop' => $deliveryWorkshop,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_delivery_workshop_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function deleteAction(Request $request, DeliveryWorkshop $deliveryWorkshop)
    {
        $deliveryWorkshopService = $this->get('app.transaction.delivery_workshop_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $deliveryWorkshopService->delete($deliveryWorkshop);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_delivery_workshop_index');
        }

        return $this->render('transaction/delivery_workshop/delete.html.twig', array(
            'deliveryWorkshop' => $deliveryWorkshop,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_delivery_workshop_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function memoAction(DeliveryWorkshop $deliveryWorkshop)
    {
        return $this->render('transaction/delivery_workshop/memo.html.twig', array(
            'deliveryWorkshop' => $deliveryWorkshop,
        ));
    }
    
}
