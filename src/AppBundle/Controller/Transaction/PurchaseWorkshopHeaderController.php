<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseWorkshopHeader;
use AppBundle\Form\Transaction\PurchaseWorkshopHeaderType;
use AppBundle\Grid\Transaction\PurchaseWorkshopHeaderGridType;
use AppBundle\Grid\Transaction\PurchaseWorkshopHeaderApprovalGridType;

/**
 * @Route("/transaction/purchase_workshop_header")
 */
class PurchaseWorkshopHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_workshop_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_ACCOUNTING_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseWorkshopHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseWorkshopHeaderGridType::class, $repository, $request);

        return $this->render('transaction/purchase_workshop_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_workshop_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ACCOUNTING_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_workshop_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_purchase_workshop_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTING_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $purchaseWorkshopHeader = new PurchaseWorkshopHeader();
        
        $purchaseWorkshopHeaderService = $this->get('app.transaction.purchase_workshop_header_form');
        $form = $this->createForm(PurchaseWorkshopHeaderType::class, $purchaseWorkshopHeader, array(
            'service' => $purchaseWorkshopHeaderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseWorkshopHeaderService->save($purchaseWorkshopHeader);

            return $this->redirectToRoute('transaction_purchase_workshop_header_show', array('id' => $purchaseWorkshopHeader->getId()));
        }

        return $this->render('transaction/purchase_workshop_header/new.'.$_format.'.twig', array(
            'purchaseWorkshopHeader' => $purchaseWorkshopHeader,
            'form' => $form->createView(),
            'purchaseWorkshopDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_workshop_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_ACCOUNTING_STAFF')")
     */
    public function showAction(PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        return $this->render('transaction/purchase_workshop_header/show.html.twig', array(
            'purchaseWorkshopHeader' => $purchaseWorkshopHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_purchase_workshop_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTING_HEAD')")
     */
    public function editAction(Request $request, PurchaseWorkshopHeader $purchaseWorkshopHeader, $_format = 'html')
    {
        $purchaseWorkshopDetailsCount = $purchaseWorkshopHeader->getPurchaseWorkshopDetails()->count();

        $purchaseWorkshopHeaderService = $this->get('app.transaction.purchase_workshop_header_form');
        $form = $this->createForm(PurchaseWorkshopHeaderType::class, $purchaseWorkshopHeader, array(
            'service' => $purchaseWorkshopHeaderService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseWorkshopHeaderService->save($purchaseWorkshopHeader);

            return $this->redirectToRoute('transaction_purchase_workshop_header_show', array('id' => $purchaseWorkshopHeader->getId()));
        }

        return $this->render('transaction/purchase_workshop_header/edit.'.$_format.'.twig', array(
            'purchaseWorkshopHeader' => $purchaseWorkshopHeader,
            'form' => $form->createView(),
            'purchaseWorkshopDetailsCount' => $purchaseWorkshopDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_workshop_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTING_HEAD')")
     */
    public function deleteAction(Request $request, PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        $purchaseWorkshopHeaderService = $this->get('app.transaction.purchase_workshop_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $purchaseWorkshopHeaderService->delete($purchaseWorkshopHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_workshop_header_index');
        }

        return $this->render('transaction/purchase_workshop_header/delete.html.twig', array(
            'purchaseWorkshopHeader' => $purchaseWorkshopHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/grid_approval", name="transaction_purchase_workshop_header_grid_approval", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_ACCOUNTING_HEAD')")
     */
    public function gridApprovalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseWorkshopHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseWorkshopHeaderApprovalGridType::class, $repository, $request);

        return $this->render('transaction/purchase_workshop_header/grid_approval.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/approval", name="transaction_purchase_workshop_header_approval")
     * @Method("GET")
     * @Security("has_role('ROLE_ACCOUNTING_HEAD')")
     */
    public function approvalAction()
    {
        return $this->render('transaction/purchase_workshop_header/approval.html.twig');
    }
    
    /**
     * @Route("/{id}/approve", name="transaction_purchase_workshop_header_approve", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTING_HEAD')")
     */
    public function approveAction(Request $request, PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        $purchaseWorkshopHeaderService = $this->get('app.transaction.purchase_workshop_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $purchaseWorkshopHeaderService->approve($purchaseWorkshopHeader, $this->getUser());

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'Transaction was successfully approved.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the transaction.'));
            }

            return $this->redirectToRoute('transaction_purchase_workshop_header_approval');
        }
        
        return $this->render('transaction/purchase_workshop_header/approve.html.twig', array(
            'purchaseWorkshopHeader' => $purchaseWorkshopHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/reject", name="transaction_purchase_workshop_header_reject", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTING_HEAD')")
     */
    public function rejectAction(Request $request, PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        $purchaseWorkshopHeaderService = $this->get('app.transaction.purchase_workshop_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $purchaseWorkshopHeaderService->reject($purchaseWorkshopHeader, $this->getUser());

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'Transaction was successfully rejected.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the transaction.'));
            }

            return $this->redirectToRoute('transaction_purchase_workshop_header_approval');
        }
        
        return $this->render('transaction/purchase_workshop_header/reject.html.twig', array(
            'purchaseWorkshopHeader' => $purchaseWorkshopHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_purchase_workshop_header_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_ACCOUNTING_STAFF')")
     */
    public function memoAction(Request $request, PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        $show = $request->query->getBoolean('show', false);

        return $this->render('transaction/purchase_workshop_header/memo.html.twig', array(
            'purchaseWorkshopHeader' => $purchaseWorkshopHeader,
            'show' => $show,
        ));
    }
}
