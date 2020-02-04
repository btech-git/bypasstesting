<?php

namespace AppBundle\Controller\Report;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceDetailGeneral;
use AppBundle\Grid\Report\SaleInvoiceDetailGeneralGridType;

/**
 * @Route("/report/sale_invoice_detail_general")
 */
class SaleInvoiceDetailGeneralController extends Controller
{
    /**
     * @Route("/grid", name="report_sale_invoice_detail_general_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceDetailGeneral::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceDetailGeneralGridType::class, $repository, $request);

        return $this->render('report/sale_invoice_detail_general/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="report_sale_invoice_detail_general_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function indexAction()
    {
        return $this->render('report/sale_invoice_detail_general/index.html.twig');
    }
    
    /**
     * @Route("/export", name="report_sale_invoice_detail_general_export")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function exportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceDetailGeneral::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceDetailGeneralGridType::class, $repository, $request);

        $excel = $this->get('phpexcel');
        $excelXmlReader = $this->get('lib.excel.xml_reader');
        $xml = $this->renderView('report/sale_invoice_detail_general/export.xml.twig', array(
            'grid' => $grid->createView(),
        ));
        $excelObject = $excelXmlReader->load($xml);
        $writer = $excel->createWriter($excelObject, 'Excel5');
        $response = $excel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'lap pembelian umum.xls');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
