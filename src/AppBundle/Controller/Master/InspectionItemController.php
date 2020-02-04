<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\InspectionItem;
use AppBundle\Form\Master\InspectionItemType;
use AppBundle\Grid\Master\InspectionItemGridType;

/**
 * @Route("/master/inspection_item")
 */
class InspectionItemController extends Controller
{
    /**
     * @Route("/grid", name="master_inspection_item_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(InspectionItem::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(InspectionItemGridType::class, $repository, $request);

        return $this->render('master/inspection_item/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_inspection_item_index")
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function indexAction()
    {
        return $this->render('master/inspection_item/index.html.twig');
    }

    /**
     * @Route("/new", name="master_inspection_item_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function newAction(Request $request)
    {
        $inspectionItem = new InspectionItem();
        $form = $this->createForm(InspectionItemType::class, $inspectionItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(InspectionItem::class);
            $repository->add($inspectionItem);

            return $this->redirectToRoute('master_inspection_item_show', array('id' => $inspectionItem->getId()));
        }

        return $this->render('master/inspection_item/new.html.twig', array(
            'inspectionItem' => $inspectionItem,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_inspection_item_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function showAction(InspectionItem $inspectionItem)
    {
        return $this->render('master/inspection_item/show.html.twig', array(
            'inspectionItem' => $inspectionItem,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_inspection_item_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function editAction(Request $request, InspectionItem $inspectionItem)
    {
        $form = $this->createForm(InspectionItemType::class, $inspectionItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(InspectionItem::class);
            $repository->update($inspectionItem);

            return $this->redirectToRoute('master_inspection_item_show', array('id' => $inspectionItem->getId()));
        }

        return $this->render('master/inspection_item/edit.html.twig', array(
            'inspectionItem' => $inspectionItem,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_inspection_item_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function deleteAction(Request $request, InspectionItem $inspectionItem)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(InspectionItem::class);
                $repository->remove($inspectionItem);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_inspection_item_index');
        }

        return $this->render('master/inspection_item/delete.html.twig', array(
            'inspectionItem' => $inspectionItem,
            'form' => $form->createView(),
        ));
    }
}
