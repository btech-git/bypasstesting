<?php

namespace AppBundle\Service\Transaction;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use LibBundle\Excel\PhpExcelObjectParser;
use LibBundle\Doctrine\EntityRepository;

class SaleInvoiceSparepartHeaderSheet
{
    private $validator;
    private $parser;
    private $saleInvoiceSparepartHeaderRepository;
    
    public function __construct(ValidatorInterface $validator, PhpExcelObjectParser $parser, EntityRepository $saleInvoiceSparepartHeaderRepository)
    {
        $this->validator = $validator;
        $this->parser = $parser;
        $this->saleInvoiceSparepartHeaderRepository = $saleInvoiceSparepartHeaderRepository;
    }
    
    public function parse($headerDataFile, $headerMappingXml, $detailDataFile, $detailMappingXml)
    {
        $numberFormatter = function($value) { return number_format(doubleval(str_replace(',', '.', $value)), 2, '.', ''); };
        $dateFormatter = function($value) { return date_create_from_format('Y-m-d H:i:s', $value); };
        
        $this->parser->setObjectMappingsFromXml($headerMappingXml);
        $this->parser->addFormatter('Number', $numberFormatter);
        $this->parser->addFormatter('Date', $dateFormatter);
        $headerData = $this->parser->load($headerDataFile);
        
        $this->parser->setObjectMappingsFromXml($detailMappingXml);
        $this->parser->addFormatter('Number', $numberFormatter);
        $detailData = $this->parser->load($detailDataFile);
        
        foreach ($headerData['references']['invoiceNumber'] as $headerInvoiceNumber => $headerIndexes) {
            foreach ($headerIndexes as $headerIndex) {
                $headerObject = $headerData['objects'][$headerIndex];
                foreach ($detailData['references']['invoiceNumber'] as $detailInvoiceNumber => $detailIndexes) {
                    foreach ($detailIndexes as $detailIndex) {
                        if ($headerInvoiceNumber === $detailInvoiceNumber) {
                            $details = $headerObject->getSaleInvoiceSparepartDetails();
                            $detailObject = $detailData['objects'][$detailIndex];
                            $detailObject->setSaleInvoiceSparepartHeader($headerObject);
                            $details->add($detailObject);
                        }
                    }
                }
            }
        }
        
        return $headerData['objects'];
    }
    
    public function validate(array $saleInvoiceSparepartHeaders)
    {
        if (empty($saleInvoiceSparepartHeaders)) {
            return false;
        }
        foreach ($saleInvoiceSparepartHeaders as $saleInvoiceSparepartHeader) {
            $errors = $this->validator->validate($saleInvoiceSparepartHeader);
            if (count($errors) > 0) {
                return false;
            }
            foreach ($saleInvoiceSparepartHeader->getSaleInvoiceSparepartDetails() as $saleInvoiceSparepartDetail) {
                $errors = $this->validator->validate($saleInvoiceSparepartDetail);
                if (count($errors) > 0) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    public function save(array $saleInvoiceSparepartHeaders)
    {
        $this->saleInvoiceSparepartHeaderRepository->add($saleInvoiceSparepartHeaders, array(
            'saleInvoiceSparepartDetails' => array('add' => true),
        ));
    }
}
