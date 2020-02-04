<?php

namespace AppBundle\Controller\Report;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseInvoiceDetailWorkshop;
use AppBundle\Grid\Report\PurchaseInvoiceDetailWorkshopGridType;

/**
 * @Route("/report/purchase_invoice_detail_workshop")
 */
class PurchaseInvoiceDetailWorkshopController extends Controller
{
    /**
     * @Route("/grid", name="report_purchase_invoice_detail_workshop_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseInvoiceDetailWorkshop::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseInvoiceDetailWorkshopGridType::class, $repository, $request);

        return $this->render('report/purchase_invoice_detail_workshop/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="report_purchase_invoice_detail_workshop_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function indexAction()
    {
        return $this->render('report/purchase_invoice_detail_workshop/index.html.twig');
    }

    /**
     * @Route("/export", name="report_purchase_invoice_detail_workshop_export")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ACCOUNTING_HEAD') or has_role('ROLE_OPERATIONAL_HEAD') or has_role('ROLE_SALES_MANAGER')")
     */
    public function exportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseInvoiceDetailWorkshop::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseInvoiceDetailWorkshopGridType::class, $repository, $request);

        $excel = $this->get('phpexcel');
        $excelXmlReader = $this->get('lib.excel.xml_reader');
        $xml = $this->renderView('report/purchase_invoice_detail_workshop/export.xml.twig', array(
            'grid' => $grid->createView(),
        ));
        $excelObject = $excelXmlReader->load($xml);
        $writer = $excel->createWriter($excelObject, 'Excel5');
        $response = $excel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'lap pembelian karoseri.xls');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
