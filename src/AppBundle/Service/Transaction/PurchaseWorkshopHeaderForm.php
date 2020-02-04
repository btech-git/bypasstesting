<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\PurchaseWorkshopHeader;
use AppBundle\Repository\Transaction\PurchaseWorkshopHeaderRepository;

class PurchaseWorkshopHeaderForm
{
    private $purchaseWorkshopHeaderRepository;
    
    public function __construct(PurchaseWorkshopHeaderRepository $purchaseWorkshopHeaderRepository)
    {
        $this->purchaseWorkshopHeaderRepository = $purchaseWorkshopHeaderRepository;
    }
    
    public function initialize(PurchaseWorkshopHeader $purchaseWorkshopHeader, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($purchaseWorkshopHeader->getId())) {
            $purchaseWorkshopHeader->setStaffFirst($staff);
            $purchaseWorkshopHeader->setApproveOrRejectStatus('');
        }
        $purchaseWorkshopHeader->setStaffLast($staff);
    }
    
    public function finalize(PurchaseWorkshopHeader $purchaseWorkshopHeader, array $params = array())
    {
        if (empty($purchaseWorkshopHeader->getId())) {
            $transactionDate = $purchaseWorkshopHeader->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastPurchaseWorkshopHeader = $this->purchaseWorkshopHeaderRepository->findRecentBy($year, $month);
                $currentPurchaseWorkshopHeader = ($lastPurchaseWorkshopHeader === null) ? $purchaseWorkshopHeader : $lastPurchaseWorkshopHeader;
                $purchaseWorkshopHeader->setCodeNumberToNext($currentPurchaseWorkshopHeader->getCodeNumber(), $year, $month);
            }
        }
        
        foreach ($purchaseWorkshopHeader->getPurchaseWorkshopDetails() as $purchaseWorkshopDetail) {
            $purchaseWorkshopDetail->setPurchaseWorkshopHeader($purchaseWorkshopHeader);
        }
        
        $this->sync($purchaseWorkshopHeader);
    }
    
    private function sync(PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        $subTotal = 0.00;
        foreach ($purchaseWorkshopHeader->getPurchaseWorkshopDetails() as $purchaseWorkshopDetail) {
            $total = $purchaseWorkshopDetail->getQuantity() * $purchaseWorkshopDetail->getUnitPrice();
            $purchaseWorkshopDetail->setTotal($total);
            $subTotal += $total;
        }
        $purchaseWorkshopHeader->setSubTotal($subTotal);
        $taxNominal = $purchaseWorkshopHeader->getIsTax() ? $subTotal * 0.1 : 0;
        $purchaseWorkshopHeader->setTaxNominal($taxNominal);
        $grandTotal = $subTotal + $taxNominal;
        $purchaseWorkshopHeader->setGrandTotal($grandTotal);
        $grandTotalReplacement = $subTotal + round($purchaseWorkshopHeader->getTaxNominalReplacement());
        $purchaseWorkshopHeader->setGrandTotalReplacement($grandTotalReplacement);
        $saleOrder = $purchaseWorkshopHeader->getSaleOrder();
        if ($saleOrder !== null) {
            $purchaseWorkshopHeader->setQuantityOrder($saleOrder->getQuantity());
        }
    }
    
    public function save(PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        if (empty($purchaseWorkshopHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseWorkshopHeader) {
                $this->purchaseWorkshopHeaderRepository->add($purchaseWorkshopHeader, array(
                    'purchaseWorkshopDetails' => array('add' => true),
                ));
            });
        } else {
            ObjectPersister::save(function() use ($purchaseWorkshopHeader) {
                $this->purchaseWorkshopHeaderRepository->update($purchaseWorkshopHeader, array(
                    'purchaseWorkshopDetails' => array('add' => true, 'remove' => true),
                ));
            });
        }
    }
    
    public function delete(PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        $this->beforeDelete($purchaseWorkshopHeader);
        if (!empty($purchaseWorkshopHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseWorkshopHeader) {
                $this->purchaseWorkshopHeaderRepository->remove($purchaseWorkshopHeader, array(
                    'purchaseWorkshopDetails' => array('remove' => true),
                ));
            });
        }
    }
    
    protected function beforeDelete(PurchaseWorkshopHeader $purchaseWorkshopHeader)
    {
        $purchaseWorkshopHeader->getPurchaseWorkshopDetails()->clear();
        $this->sync($purchaseWorkshopHeader);
    }
    
    public function approve(PurchaseWorkshopHeader $purchaseWorkshopHeader, $staff)
    {
        if ($purchaseWorkshopHeader->getApproveOrRejectStatus() === '') {
            $purchaseWorkshopHeader->setApproveOrRejectStatus('A');
            $purchaseWorkshopHeader->setStaffApproveOrReject($staff);
            
            ObjectPersister::save(function() use ($purchaseWorkshopHeader) {
                $this->purchaseWorkshopHeaderRepository->update($purchaseWorkshopHeader);
            });
        }
    }
    
    public function reject(PurchaseWorkshopHeader $purchaseWorkshopHeader, $staff)
    {
        if ($purchaseWorkshopHeader->getApproveOrRejectStatus() === '') {
            $purchaseWorkshopHeader->setApproveOrRejectStatus('R');
            $purchaseWorkshopHeader->setStaffApproveOrReject($staff);
            
            ObjectPersister::save(function() use ($purchaseWorkshopHeader) {
                $this->purchaseWorkshopHeaderRepository->update($purchaseWorkshopHeader);
            });
        }
    }
}
