<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;

class PurchaseInvoiceHeaderForm
{
    private $purchaseInvoiceHeaderRepository;
    private $journalLedgerRepository;
    
    public function __construct(PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository, JournalLedgerRepository $journalLedgerRepository)
    {
        $this->purchaseInvoiceHeaderRepository = $purchaseInvoiceHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
    }
    
    public function initialize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($purchaseInvoiceHeader->getId())) {
            $purchaseInvoiceHeader->setStaffFirst($staff);
        }
        $purchaseInvoiceHeader->setStaffLast($staff);
    }
    
    public function finalize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $params = array())
    {
        if (empty($purchaseInvoiceHeader->getId())) {
            $transactionDate = $purchaseInvoiceHeader->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastPurchaseInvoiceHeaderApplication = $this->purchaseInvoiceHeaderRepository->findRecentBy($year, $month);
                $currentPurchaseInvoiceHeader = ($lastPurchaseInvoiceHeaderApplication === null) ? $purchaseInvoiceHeader : $lastPurchaseInvoiceHeaderApplication;
                $purchaseInvoiceHeader->setCodeNumberToNext($currentPurchaseInvoiceHeader->getCodeNumber(), $year, $month);
            }
        }
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $purchaseInvoiceDetail->setPurchaseInvoiceHeader($purchaseInvoiceHeader);
        }
        $this->sync($purchaseInvoiceHeader);
    }
    
    private function sync(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $subTotal = 0.00;
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $total = $purchaseInvoiceDetail->getQuantity() * $purchaseInvoiceDetail->getUnitPrice();
            $purchaseInvoiceDetail->setTotal($total);
            $subTotal += $total;
        }
        $purchaseInvoiceHeader->setSubTotal($subTotal);
        $taxNominal = $purchaseInvoiceHeader->getIsTax() ? $subTotal * 0.1 : 0;
        $purchaseInvoiceHeader->setTaxNominal($taxNominal);
        $grandTotal = $subTotal + $taxNominal;
        $purchaseInvoiceHeader->setGrandTotal($grandTotal);
        
        $purchaseWorkshopHeader = $purchaseInvoiceHeader->getPurchaseWorkshopHeader();
        if ($purchaseInvoiceHeader->getIsPurchaseWorkshopHeader() && $purchaseWorkshopHeader !== null) {
            $purchaseInvoiceHeader->setSupplier($purchaseWorkshopHeader->getSupplier());
            $purchaseInvoiceDetails = $purchaseInvoiceHeader->getPurchaseInvoiceDetails();
            $purchaseInvoiceDetails->clear();
            foreach ($purchaseWorkshopHeader->getPurchaseWorkshopDetails() as $purchaseWorkshopDetail) {
                $purchaseInvoiceDetail = new \AppBundle\Entity\Transaction\PurchaseInvoiceDetail();
                $purchaseInvoiceDetail->setItemName($purchaseWorkshopDetail->getItemName());
                $purchaseInvoiceDetail->setQuantity($purchaseWorkshopDetail->getQuantity());
                $purchaseInvoiceDetail->setUnitPrice($purchaseWorkshopDetail->getUnitPrice());
                $purchaseInvoiceDetail->setTotal($purchaseWorkshopDetail->getTotal());
                $purchaseInvoiceDetail->setPurchaseInvoiceHeader($purchaseInvoiceHeader);
                $purchaseInvoiceDetails->add($purchaseInvoiceDetail);
            }
        } else {
            $purchaseInvoiceHeader->setPurchaseWorkshopHeader(null);
        }
        $purchaseInvoiceHeader->sync();
    }
    
    public function save(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        if (empty($purchaseInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->update($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, true);
            });
        }
    }
    
    public function delete(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $this->beforeDelete($purchaseInvoiceHeader);
        if (!empty($purchaseInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->remove($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, true);
            });
        }
    }
    
    protected function beforeDelete(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeader->getPurchaseInvoiceDetails()->clear();
        $this->sync($purchaseInvoiceHeader);
    }
    
    private function markJournalLedgers(DepositHeader $depositHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_DEPOSIT,
            'codeNumberYear' => $depositHeader->getCodeNumberYear(),
            'codeNumberMonth' => $depositHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $depositHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        foreach ($depositHeader->getDepositDetails() as $depositDetail) {
            if ($depositDetail->getAmount() > 0) {
                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($depositHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($depositHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_DEPOSIT);
                $journalLedgerCredit->setTransactionSubject($depositDetail->getMemo());
                $journalLedgerCredit->setNote($depositHeader->getNote());
                $journalLedgerCredit->setDebit(0);
                $journalLedgerCredit->setCredit($depositDetail->getAmount());
                $journalLedgerCredit->setAccount($depositDetail->getAccount());
                $journalLedgerCredit->setStaff($depositHeader->getStaff());
                $this->journalLedgerRepository->add($journalLedgerCredit);
                
                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($depositHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($depositHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_DEPOSIT);
                $journalLedgerDebit->setTransactionSubject($depositDetail->getMemo());
                $journalLedgerDebit->setNote($depositHeader->getNote());
                $journalLedgerDebit->setDebit($depositDetail->getAmount());
                $journalLedgerDebit->setCredit(0);
                $journalLedgerDebit->setAccount($depositHeader->getAccount());
                $journalLedgerDebit->setStaff($depositHeader->getStaff());
                $this->journalLedgerRepository->add($journalLedgerDebit);
            }
        }
    }
}
