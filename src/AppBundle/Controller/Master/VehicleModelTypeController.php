<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\VehicleModelType;
use AppBundle\Form\Master\VehicleModelTypeType;
use AppBundle\Grid\Master\VehicleModelTypeGridType;

/**
 * @Route("/master/vehicle_model_type")
 */
class VehicleModelTypeController extends Controller
{
    /**
     * @Route("/grid", name="master_vehicle_model_type_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(VehicleModelType::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(VehicleModelTypeGridType::class, $repository, $request);

        return $this->render('master/vehicle_model_type/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_vehicle_model_type_index")
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function indexAction()
    {
        return $this->render('master/vehicle_model_type/index.html.twig');
    }

    /**
     * @Route("/new", name="master_vehicle_model_type_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function newAction(Request $request)
    {
        $vehicleModelType = new VehicleModelType();
        $form = $this->createForm(VehicleModelTypeType::class, $vehicleModelType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(VehicleModelType::class);
            $repository->add($vehicleModelType);

            return $this->redirectToRoute('master_vehicle_model_type_show', array('id' => $vehicleModelType->getId()));
        }

        return $this->render('master/vehicle_model_type/new.html.twig', array(
            'vehicleModelType' => $vehicleModelType,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_vehicle_model_type_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function showAction(VehicleModelType $vehicleModelType)
    {
        return $this->render('master/vehicle_model_type/show.html.twig', array(
            'vehicleModelType' => $vehicleModelType,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_vehicle_model_type_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function editAction(Request $request, VehicleModelType $vehicleModelType)
    {
        $form = $this->createForm(VehicleModelTypeType::class, $vehicleModelType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(VehicleModelType::class);
            $repository->update($vehicleModelType);

            return $this->redirectToRoute('master_vehicle_model_type_show', array('id' => $vehicleModelType->getId()));
        }

        return $this->render('master/vehicle_model_type/edit.html.twig', array(
            'vehicleModelType' => $vehicleModelType,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_vehicle_model_type_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function deleteAction(Request $request, VehicleModelType $vehicleModelType)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(VehicleModelType::class);
                $repository->remove($vehicleModelType);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_vehicle_model_type_index');
        }

        return $this->render('master/vehicle_model_type/delete.html.twig', array(
            'vehicleModelType' => $vehicleModelType,
            'form' => $form->createView(),
        ));
    }
}
