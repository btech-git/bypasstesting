<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;
use AppBundle\Grid\Common\SaleInvoiceDownpaymentGridType;

/**
 * @Route("/common/sale_invoice_downpayment")
 */
class SaleInvoiceDownpaymentController extends Controller
{
    /**
     * @Route("/grid", name="common_sale_invoice_downpayment_grid", condition="request.isXmlHttpRequest()")
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
        $repository = $em->getRepository(SaleInvoiceDownpayment::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceDownpaymentGridType::class, $repository, $request, $options);

        return $this->render('common/sale_invoice_downpayment/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}
