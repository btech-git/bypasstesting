<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleDiscountApplication;
use AppBundle\Form\Transaction\SaleDiscountApplicationType;
use AppBundle\Grid\Transaction\SaleDiscountApplicationGridType;

/**
 * @Route("/transaction/sale_discount_application")
 */
class SaleDiscountApplicationController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_discount_application_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleDiscountApplication::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleDiscountApplicationGridType::class, $repository, $request);

        return $this->render('transaction/sale_discount_application/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_discount_application_index")
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_discount_application/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_discount_application_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $saleDiscountApplication = new SaleDiscountApplication();

        $saleDiscountApplicationService = $this->get('app.transaction.sale_discount_application_form');
        $form = $this->createForm(SaleDiscountApplicationType::class, $saleDiscountApplication, array(
            'service' => $saleDiscountApplicationService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleDiscountApplicationService->save($saleDiscountApplication);

            return $this->redirectToRoute('transaction_sale_discount_application_show', array('id' => $saleDiscountApplication->getId()));
        }

        return $this->render('transaction/sale_discount_application/new.'.$_format.'.twig', array(
            'saleDiscountApplication' => $saleDiscountApplication,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_discount_application_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function showAction(SaleDiscountApplication $saleDiscountApplication)
    {
        return $this->render('transaction/sale_discount_application/show.html.twig', array(
            'saleDiscountApplication' => $saleDiscountApplication,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_sale_discount_application_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function editAction(Request $request, SaleDiscountApplication $saleDiscountApplication, $_format = 'html')
    {
        $saleDiscountApplicationService = $this->get('app.transaction.sale_discount_application_form');
        $form = $this->createForm(SaleDiscountApplicationType::class, $saleDiscountApplication, array(
            'service' => $saleDiscountApplicationService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleDiscountApplicationService->save($saleDiscountApplication);

            return $this->redirectToRoute('transaction_sale_discount_application_show', array('id' => $saleDiscountApplication->getId()));
        }

        return $this->render('transaction/sale_discount_application/edit.'.$_format.'.twig', array(
            'saleDiscountApplication' => $saleDiscountApplication,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_discount_application_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALES_MANAGER')")
     */
    public function deleteAction(Request $request, SaleDiscountApplication $saleDiscountApplication)
    {
        $saleDiscountApplicationService = $this->get('app.transaction.sale_discount_application_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleDiscountApplicationService->delete($saleDiscountApplication);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_discount_application_index');
        }

        return $this->render('transaction/sale_discount_application/delete.html.twig', array(
            'saleDiscountApplication' => $saleDiscountApplication,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/memo", name="transaction_sale_discount_application_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALES_STAFF')")
     */
    public function memoAction(SaleDiscountApplication $saleDiscountApplication)
    {
        return $this->render('transaction/sale_discount_application/memo.html.twig', array(
            'saleDiscountApplication' => $saleDiscountApplication,
        ));
    }
}
