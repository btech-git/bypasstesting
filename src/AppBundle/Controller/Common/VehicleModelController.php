<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\VehicleModel;
use AppBundle\Grid\Common\VehicleModelGridType;

/**
 * @Route("/common/vehicle_model")
 */
class VehicleModelController extends Controller
{
    /**
     * @Route("/grid", name="common_vehicle_model_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function gridAction(Request $request)
    {
        $options = array();
        if ($request->query->has('form')) {
            $options['form'] = $request->query->get('form');
        }
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(VehicleModel::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(VehicleModelGridType::class, $repository, $request, $options);

        return $this->render('common/vehicle_model/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}
