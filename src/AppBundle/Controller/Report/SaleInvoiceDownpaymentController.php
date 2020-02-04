<?php

namespace AppBundle\Controller\Report;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;
use AppBundle\Grid\Report\SaleInvoiceDownpaymentGridType;

/**
 * @Route("/report/sale_invoice_downpayment")
 */
class SaleInvoiceDownpaymentController extends Controller
{
    /**
     * @Route("/grid", name="report_sale_invoice_downpayment_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceDownpayment::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceDownpaymentGridType::class, $repository, $request);

        return $this->render('report/sale_invoice_downpayment/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="report_sale_invoice_downpayment_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function indexAction()
    {
        return $this->render('report/sale_invoice_downpayment/index.html.twig');
    }
    /**
     * @Route("/export", name="report_sale_invoice_downpayment_export")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function exportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceDownpayment::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceDownpaymentGridType::class, $repository, $request);

        $excel = $this->get('phpexcel');
        $excelXmlReader = $this->get('lib.excel.xml_reader');
        $xml = $this->renderView('report/sale_invoice_downpayment/export.xml.twig', array(
            'grid' => $grid->createView(),
        ));
        $excelObject = $excelXmlReader->load($xml);
        $writer = $excel->createWriter($excelObject, 'Excel5');
        $response = $excel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'downpayment penjualan.xls');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
