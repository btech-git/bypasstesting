<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceSparepartHeader;
use AppBundle\Grid\Transaction\SaleInvoiceSparepartHeaderGridType;

/**
 * @Route("/transaction/sale_invoice_sparepart_header")
 */
class SaleInvoiceSparepartHeaderController extends Controller
{
    /**
     * @Route("/import", name="transaction_sale_invoice_sparepart_header_import")
     * @Method({"GET", "POST"})
     */
    public function importAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('headerDataFile', FileType::class, array('constraints' => array(new NotNull())))
            ->add('detailDataFile', FileType::class, array('constraints' => array(new NotNull())))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            if ($formData['headerDataFile']->isValid() && $formData['detailDataFile']->isValid()) {
                $saleInvoiceSparepartHeaderSheet = $this->get('app.transaction.sale_invoice_sparepart_header_sheet');

                $headerMappingXml = $this->renderView('transaction/sale_invoice_sparepart_header/import_mapping_header.xml.twig');
                $detailMappingXml = $this->renderView('transaction/sale_invoice_sparepart_header/import_mapping_detail.xml.twig');
                $objects = $saleInvoiceSparepartHeaderSheet->parse($formData['headerDataFile']->getPathname(), $headerMappingXml, $formData['detailDataFile']->getPathname(), $detailMappingXml);

                if ($saleInvoiceSparepartHeaderSheet->validate($objects)) {
                    $saleInvoiceSparepartHeaderSheet->save($objects);
                    $this->addFlash('success', array('title' => 'Success!', 'message' => 'The records was imported successfully.'));
                } else {
                    $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to import the records.'));
                }

                return $this->redirectToRoute('transaction_sale_invoice_sparepart_header_import');
            }
        }

        return $this->render('transaction/sale_invoice_sparepart_header/import.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/grid", name="transaction_sale_invoice_sparepart_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceSparepartHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceSparepartHeaderGridType::class, $repository, $request);

        return $this->render('transaction/sale_invoice_sparepart_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_invoice_sparepart_header_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_invoice_sparepart_header/index.html.twig');
    }

    /**
     * @Route("/{id}", name="transaction_sale_invoice_sparepart_header_show")
     * @Method("GET")
     */
    public function showAction(SaleInvoiceSparepartHeader $saleInvoiceSparepartHeader)
    {
        return $this->render('transaction/sale_invoice_sparepart_header/show.html.twig', array(
            'saleInvoiceSparepartHeader' => $saleInvoiceSparepartHeader,
        ));
    }
}
