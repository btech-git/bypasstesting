<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ReceiveWorkshop;
use AppBundle\Grid\Common\ReceiveWorkshopGridType;

/**
 * @Route("/common/receive_workshop")
 */
class ReceiveWorkshopController extends Controller
{
    /**
     * @Route("/grid", name="common_receive_workshop_grid", condition="request.isXmlHttpRequest()")
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
        $repository = $em->getRepository(ReceiveWorkshop::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ReceiveWorkshopGridType::class, $repository, $request, $options);

        return $this->render('common/receive_workshop/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}
