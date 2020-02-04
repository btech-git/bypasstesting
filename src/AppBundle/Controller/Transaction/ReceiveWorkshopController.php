<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ReceiveWorkshop;
use AppBundle\Form\Transaction\ReceiveWorkshopType;
use AppBundle\Grid\Transaction\ReceiveWorkshopGridType;

/**
 * @Route("/transaction/receive_workshop")
 */
class ReceiveWorkshopController extends Controller
{
    /**
     * @Route("/grid", name="transaction_receive_workshop_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ReceiveWorkshop::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ReceiveWorkshopGridType::class, $repository, $request);

        return $this->render('transaction/receive_workshop/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_receive_workshop_index")
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/receive_workshop/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_receive_workshop_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $receiveWorkshop = new ReceiveWorkshop();
        
        $receiveWorkshopService = $this->get('app.transaction.receive_workshop_form');
        $form = $this->createForm(ReceiveWorkshopType::class, $receiveWorkshop, array(
            'service' => $receiveWorkshopService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $receiveWorkshopService->save($receiveWorkshop);

            return $this->redirectToRoute('transaction_receive_workshop_show', array('id' => $receiveWorkshop->getId()));
        }

        return $this->render('transaction/receive_workshop/new.'.$_format.'.twig', array(
            'receiveWorkshop' => $receiveWorkshop,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_receive_workshop_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function showAction(ReceiveWorkshop $receiveWorkshop)
    {
        return $this->render('transaction/receive_workshop/show.html.twig', array(
            'receiveWorkshop' => $receiveWorkshop,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_receive_workshop_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, ReceiveWorkshop $receiveWorkshop, $_format = 'html')
    {
        $receiveWorkshopService = $this->get('app.transaction.receive_workshop_form');
        $form = $this->createForm(ReceiveWorkshopType::class, $receiveWorkshop, array(
            'service' => $receiveWorkshopService,
            'init' => array('staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $receiveWorkshopService->save($receiveWorkshop);

            return $this->redirectToRoute('transaction_receive_workshop_show', array('id' => $receiveWorkshop->getId()));
        }

        return $this->render('transaction/receive_workshop/edit.'.$_format.'.twig', array(
            'receiveWorkshop' => $receiveWorkshop,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_receive_workshop_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, ReceiveWorkshop $receiveWorkshop)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(ReceiveWorkshop::class);
                $repository->remove($receiveWorkshop);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_receive_workshop_index');
        }

        return $this->render('transaction/receive_workshop/delete.html.twig', array(
            'receiveWorkshop' => $receiveWorkshop,
            'form' => $form->createView(),
        ));
    }
}
