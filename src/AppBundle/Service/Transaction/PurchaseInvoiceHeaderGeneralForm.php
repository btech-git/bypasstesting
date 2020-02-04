<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class PurchaseInvoiceHeaderGeneralForm
{
    private $purchaseInvoiceHeaderRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->purchaseInvoiceHeaderRepository = $purchaseInvoiceHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $params = array())
    {
        list($date, $staff) = array($params['date'], $params['staff']);
        
        if (empty($purchaseInvoiceHeader->getId())) {
            $createdDate = date_create_from_format('Y-m-d', $date);
            $purchaseInvoiceHeader->setCreatedDate($createdDate);
            $purchaseInvoiceHeader->setDueDate($createdDate);
            $purchaseInvoiceHeader->setReceiveWorkshop(null);
            $purchaseInvoiceHeader->setBusinessType(PurchaseInvoiceHeader::BUSINESS_TYPE_GENERAL);
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
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetailGenerals() as $purchaseInvoiceDetailGeneral) {
            $purchaseInvoiceDetailGeneral->setPurchaseInvoiceHeader($purchaseInvoiceHeader);
        }
        $this->sync($purchaseInvoiceHeader);
    }
    
    private function sync(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeader->sync();
    }
    
    public function save(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        if (empty($purchaseInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetailGenerals' => array('add' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->update($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetailGenerals' => array('add' => true, 'remove' => true),
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
                    'purchaseInvoiceDetailGenerals' => array('remove' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, false);
            });
        }
    }
    
    protected function beforeDelete(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeader->getPurchaseInvoiceDetailGenerals()->clear();
        $this->sync($purchaseInvoiceHeader);
    }
    
    private function markJournalLedgers(PurchaseInvoiceHeader $purchaseInvoiceHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_PAYABLE,
            'codeNumberYear' => $purchaseInvoiceHeader->getCodeNumberYear(),
            'codeNumberMonth' => $purchaseInvoiceHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $purchaseInvoiceHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        if ($addForHeader && $purchaseInvoiceHeader->getGrandTotal() > 0) {
            $accountInventorySparepart = $this->accountRepository->findInventorySparepartRecord();
            $accountPayableSparepart = $this->accountRepository->findPayableSparepartRecord();
            
            $journalLedgerDebit = new JournalLedger();
            $journalLedgerDebit->setCodeNumber($purchaseInvoiceHeader->getCodeNumber());
            $journalLedgerDebit->setTransactionDate($purchaseInvoiceHeader->getTransactionDate());
            $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE);
            $journalLedgerDebit->setTransactionCategory(PurchaseInvoiceHeader::BUSINESS_TYPE_GENERAL);
            $journalLedgerDebit->setTransactionSubject($purchaseInvoiceHeader->getSupplier());
            $journalLedgerDebit->setNote($purchaseInvoiceHeader->getNote());
            $journalLedgerDebit->setDebit($purchaseInvoiceHeader->getGrandTotal());
            $journalLedgerDebit->setCredit(0);
            $journalLedgerDebit->setAccount($accountInventorySparepart);
            $journalLedgerDebit->setStaff($purchaseInvoiceHeader->getStaffFirst());
            $journalLedgerDebit->setPurchaseDeliveryOrder(null);
            $this->journalLedgerRepository->add($journalLedgerDebit);

            $journalLedgerCredit = new JournalLedger();
            $journalLedgerCredit->setCodeNumber($purchaseInvoiceHeader->getCodeNumber());
            $journalLedgerCredit->setTransactionDate($purchaseInvoiceHeader->getTransactionDate());
            $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE);
            $journalLedgerCredit->setTransactionCategory(PurchaseInvoiceHeader::BUSINESS_TYPE_GENERAL);
            $journalLedgerCredit->setTransactionSubject($purchaseInvoiceHeader->getSupplier());
            $journalLedgerCredit->setNote($purchaseInvoiceHeader->getNote());
            $journalLedgerCredit->setDebit(0);
            $journalLedgerCredit->setCredit($purchaseInvoiceHeader->getGrandTotal());
            $journalLedgerCredit->setAccount($accountPayableSparepart);
            $journalLedgerCredit->setStaff($purchaseInvoiceHeader->getStaffFirst());
            $journalLedgerCredit->setPurchaseDeliveryOrder(null);
            $this->journalLedgerRepository->add($journalLedgerCredit);
        }
    }
}
