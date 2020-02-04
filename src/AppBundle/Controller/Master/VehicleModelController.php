<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\VehicleModel;
use AppBundle\Form\Master\VehicleModelType;
use AppBundle\Grid\Master\VehicleModelGridType;

/**
 * @Route("/master/vehicle_model")
 */
class VehicleModelController extends Controller
{
    /**
     * @Route("/grid", name="master_vehicle_model_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(VehicleModel::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(VehicleModelGridType::class, $repository, $request);

        return $this->render('master/vehicle_model/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_vehicle_model_index")
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function indexAction()
    {
        return $this->render('master/vehicle_model/index.html.twig');
    }

    /**
     * @Route("/new", name="master_vehicle_model_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function newAction(Request $request)
    {
        $vehicleModel = new VehicleModel();
        $form = $this->createForm(VehicleModelType::class, $vehicleModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(VehicleModel::class);
            $repository->add($vehicleModel);

            return $this->redirectToRoute('master_vehicle_model_show', array('id' => $vehicleModel->getId()));
        }

        return $this->render('master/vehicle_model/new.html.twig', array(
            'vehicleModel' => $vehicleModel,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_vehicle_model_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function showAction(VehicleModel $vehicleModel)
    {
        return $this->render('master/vehicle_model/show.html.twig', array(
            'vehicleModel' => $vehicleModel,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_vehicle_model_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function editAction(Request $request, VehicleModel $vehicleModel)
    {
        $form = $this->createForm(VehicleModelType::class, $vehicleModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(VehicleModel::class);
            $repository->update($vehicleModel);

            return $this->redirectToRoute('master_vehicle_model_show', array('id' => $vehicleModel->getId()));
        }

        return $this->render('master/vehicle_model/edit.html.twig', array(
            'vehicleModel' => $vehicleModel,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_vehicle_model_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function deleteAction(Request $request, VehicleModel $vehicleModel)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(VehicleModel::class);
                $repository->remove($vehicleModel);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_vehicle_model_index');
        }

        return $this->render('master/vehicle_model/delete.html.twig', array(
            'vehicleModel' => $vehicleModel,
            'form' => $form->createView(),
        ));
    }
}
