<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\DeliveryInspectionHeader;
use AppBundle\Grid\Common\DeliveryInspectionHeaderGridType;

/**
 * @Route("/common/delivery_inspection_header")
 */
class DeliveryInspectionHeaderController extends Controller
{
    /**
     * @Route("/grid", name="common_delivery_inspection_header_grid", condition="request.isXmlHttpRequest()")
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
        $repository = $em->getRepository(DeliveryInspectionHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DeliveryInspectionHeaderGridType::class, $repository, $request, $options);

        return $this->render('common/delivery_inspection_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}
