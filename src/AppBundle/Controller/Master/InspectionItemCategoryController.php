<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\InspectionItemCategory;
use AppBundle\Form\Master\InspectionItemCategoryType;
use AppBundle\Grid\Master\InspectionItemCategoryGridType;

/**
 * @Route("/master/inspection_item_category")
 */
class InspectionItemCategoryController extends Controller
{
    /**
     * @Route("/grid", name="master_inspection_item_category_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(InspectionItemCategory::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(InspectionItemCategoryGridType::class, $repository, $request);

        return $this->render('master/inspection_item_category/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_inspection_item_category_index")
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function indexAction()
    {
        return $this->render('master/inspection_item_category/index.html.twig');
    }

    /**
     * @Route("/new", name="master_inspection_item_category_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function newAction(Request $request)
    {
        $inspectionItemCategory = new InspectionItemCategory();
        $form = $this->createForm(InspectionItemCategoryType::class, $inspectionItemCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(InspectionItemCategory::class);
            $repository->add($inspectionItemCategory);

            return $this->redirectToRoute('master_inspection_item_category_show', array('id' => $inspectionItemCategory->getId()));
        }

        return $this->render('master/inspection_item_category/new.html.twig', array(
            'inspectionItemCategory' => $inspectionItemCategory,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_inspection_item_category_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function showAction(InspectionItemCategory $inspectionItemCategory)
    {
        return $this->render('master/inspection_item_category/show.html.twig', array(
            'inspectionItemCategory' => $inspectionItemCategory,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_inspection_item_category_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function editAction(Request $request, InspectionItemCategory $inspectionItemCategory)
    {
        $form = $this->createForm(InspectionItemCategoryType::class, $inspectionItemCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(InspectionItemCategory::class);
            $repository->update($inspectionItemCategory);

            return $this->redirectToRoute('master_inspection_item_category_show', array('id' => $inspectionItemCategory->getId()));
        }

        return $this->render('master/inspection_item_category/edit.html.twig', array(
            'inspectionItemCategory' => $inspectionItemCategory,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_inspection_item_category_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function deleteAction(Request $request, InspectionItemCategory $inspectionItemCategory)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(InspectionItemCategory::class);
                $repository->remove($inspectionItemCategory);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_inspection_item_category_index');
        }

        return $this->render('master/inspection_item_category/delete.html.twig', array(
            'inspectionItemCategory' => $inspectionItemCategory,
            'form' => $form->createView(),
        ));
    }
}
