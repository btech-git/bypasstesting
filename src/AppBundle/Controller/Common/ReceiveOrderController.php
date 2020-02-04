<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ReceiveOrder;
use AppBundle\Grid\Common\ReceiveOrderGridType;

/**
 * @Route("/common/receive_order")
 */
class ReceiveOrderController extends Controller
{
    /**
     * @Route("/grid", name="common_receive_order_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function gridAction(Request $request)
    {
        $options = array();
        if ($request->query->has('form')) {
            $options['form'] = $request->query->get('form');
        }
        if ($request->query->has('options')) {
            $options['options'] = $request->query->get('options');
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ReceiveOrder::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ReceiveOrderGridType::class, $repository, $request, $options);

        return $this->render('common/receive_order/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}
