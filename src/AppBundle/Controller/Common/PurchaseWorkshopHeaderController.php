<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseWorkshopHeader;
use AppBundle\Grid\Common\PurchaseWorkshopHeaderGridType;

/**
 * @Route("/common/purchase_workshop_header")
 */
class PurchaseWorkshopHeaderController extends Controller
{
    /**
     * @Route("/grid", name="common_purchase_workshop_header_grid", condition="request.isXmlHttpRequest()")
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
        $repository = $em->getRepository(PurchaseWorkshopHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseWorkshopHeaderGridType::class, $repository, $request, $options);

        return $this->render('common/purchase_workshop_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}
