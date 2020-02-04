<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\Supplier;
use AppBundle\Grid\Common\SupplierGridType;

/**
 * @Route("/common/supplier")
 */
class SupplierController extends Controller
{
    /**
     * @Route("/grid", name="common_supplier_grid", condition="request.isXmlHttpRequest()")
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
        $repository = $em->getRepository(Supplier::class);
        
        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SupplierGridType::class, $repository, $request, $options);

        return $this->render('common/supplier/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}
