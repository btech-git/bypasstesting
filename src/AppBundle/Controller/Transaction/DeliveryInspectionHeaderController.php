<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\Criteria;
use AppBundle\Entity\Transaction\DeliveryInspectionHeader;
use AppBundle\Entity\Transaction\DeliveryInspectionDetail;
use AppBundle\Entity\Master\InspectionItem;
use AppBundle\Form\Transaction\DeliveryInspectionHeaderType;
use AppBundle\Grid\Transaction\DeliveryInspectionHeaderGridType;

/**
 * @Route("/transaction/delivery_inspection_header")
 */
class DeliveryInspectionHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_delivery_inspection_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(DeliveryInspectionHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DeliveryInspectionHeaderGridType::class, $repository, $request);

        return $this->render('transaction/delivery_inspection_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_delivery_inspection_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('transaction/delivery_inspection_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_delivery_inspection_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function newAction(Request $request)
    {
        $deliveryInspectionHeader = new DeliveryInspectionHeader();

        $deliveryInspectionHeaderService = $this->get('app.transaction.delivery_inspection_header_form');
        $form = $this->createForm(DeliveryInspectionHeaderType::class, $deliveryInspectionHeader, array(
            'service' => $deliveryInspectionHeaderService,
            'init' => array('staff' => $this->getUser()),
            'inspectionItemRepository' => $this->getDoctrine()->getManager()->getRepository(InspectionItem::class),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deliveryInspectionHeaderService->save($deliveryInspectionHeader);

            return $this->redirectToRoute('transaction_delivery_inspection_header_show', array('id' => $deliveryInspectionHeader->getId()));
        }

        return $this->render('transaction/delivery_inspection_header/new.html.twig', array(
            'deliveryInspectionHeader' => $deliveryInspectionHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_delivery_inspection_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function showAction(DeliveryInspectionHeader $deliveryInspectionHeader)
    {
        $names = array('deliveryInspectionDetail', 'inspectionItem', 'inspectionItemCategory');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }
        $expr = Criteria::expr();
        $associations = array(
            'inspectionItem' => array('criteria' => $criteria['inspectionItem'], 'order' => 2, 'associations' => array(
                'inspectionItemCategory' => array('criteria' => $criteria['inspectionItemCategory'], 'order' => 1),
            )),
        );
        $criteria['deliveryInspectionDetail']->andWhere($expr->eq('deliveryInspectionHeader', $deliveryInspectionHeader));
        $criteria['inspectionItemCategory']->orderBy(array('id' => Criteria::ASC));
        $criteria['inspectionItem']->orderBy(array('id' => Criteria::ASC));
        $deliveryInspectionDetailRepository = $this->getDoctrine()->getManager()->getRepository(DeliveryInspectionDetail::class);
        $deliveryInspectionDetails = $deliveryInspectionDetailRepository->match($criteria['deliveryInspectionDetail'], $associations);

        return $this->render('transaction/delivery_inspection_header/show.html.twig', array(
            'deliveryInspectionHeader' => $deliveryInspectionHeader,
            'deliveryInspectionDetails' => $deliveryInspectionDetails,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_delivery_inspection_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function editAction(Request $request, DeliveryInspectionHeader $deliveryInspectionHeader)
    {
        $deliveryInspectionHeaderService = $this->get('app.transaction.delivery_inspection_header_form');
        $form = $this->createForm(DeliveryInspectionHeaderType::class, $deliveryInspectionHeader, array(
            'service' => $deliveryInspectionHeaderService,
            'init' => array('staff' => $this->getUser()),
            'inspectionItemRepository' => $this->getDoctrine()->getManager()->getRepository(InspectionItem::class),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deliveryInspectionHeaderService->save($deliveryInspectionHeader);

            return $this->redirectToRoute('transaction_delivery_inspection_header_show', array('id' => $deliveryInspectionHeader->getId()));
        }

        return $this->render('transaction/delivery_inspection_header/edit.html.twig', array(
            'deliveryInspectionHeader' => $deliveryInspectionHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_delivery_inspection_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OPERATIONAL_HEAD')")
     */
    public function deleteAction(Request $request, DeliveryInspectionHeader $deliveryInspectionHeader)
    {
        $deliveryInspectionHeaderService = $this->get('app.transaction.delivery_inspection_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $deliveryInspectionHeaderService->save($deliveryInspectionHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_delivery_inspection_header_index');
        }

        return $this->render('transaction/delivery_inspection_header/delete.html.twig', array(
            'deliveryInspectionHeader' => $deliveryInspectionHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/memo", name="transaction_delivery_inspection_header_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTORY_STAFF')")
     */
    public function memoAction(DeliveryInspectionHeader $deliveryInspectionHeader)
    {
        $names = array('inspectionItem', 'inspectionItemCategory');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }
        $associations = array(
            'inspectionItemCategory' => array('criteria' => $criteria['inspectionItemCategory'], 'order' => 1),
        );
        $criteria['inspectionItemCategory']->orderBy(array('id' => Criteria::ASC));
        $criteria['inspectionItem']->orderBy(array('id' => Criteria::ASC));
        $inspectionItemRepository = $this->getDoctrine()->getManager()->getRepository(InspectionItem::class);
        $inspectionItems = $inspectionItemRepository->match($criteria['inspectionItem'], $associations);

        return $this->render('transaction/delivery_inspection_header/memo.html.twig', array(
            'deliveryInspectionHeader' => $deliveryInspectionHeader,
            'inspectionItems' => $inspectionItems,
        ));
    }
}
