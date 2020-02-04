<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\PurchasePaymentHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\PurchasePaymentHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class PurchasePaymentHeaderForm
{
    private $purchasePaymentHeaderRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->purchasePaymentHeaderRepository = $purchasePaymentHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(PurchasePaymentHeader $purchasePaymentHeader, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($purchasePaymentHeader->getId())) {
            $purchasePaymentHeader->setStaffFirst($staff);
        }
        $purchasePaymentHeader->setStaffLast($staff);
    }
    
    public function finalize(PurchasePaymentHeader $purchasePaymentHeader, array $params = array())
    {
        if (empty($purchasePaymentHeader->getId())) {
            $transactionDate = $purchasePaymentHeader->getTransactionDate();
            $purchasePaymentDetails = $purchasePaymentHeader->getPurchasePaymentDetails();
            if ($transactionDate !== null && count($purchasePaymentDetails) > 0) {
                $accountCode = $purchasePaymentDetails[0]->getAccount()->getCode();
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastPurchasePaymentHeaderApplication = $this->purchasePaymentHeaderRepository->findRecentBy($year, $month, $accountCode);
                $currentPurchasePaymentHeader = ($lastPurchasePaymentHeaderApplication === null) ? $purchasePaymentHeader : $lastPurchasePaymentHeaderApplication;
                $purchasePaymentHeader->setCodeNumberToNext($currentPurchasePaymentHeader->getCodeNumber(), $year, $month, $accountCode);
            }
        }
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchasePaymentDetail->setPurchasePaymentHeader($purchasePaymentHeader);
        }
        $this->sync($purchasePaymentHeader);
    }
    
    private function sync(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $totalAmount = 0.00;
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $totalAmount += $purchasePaymentDetail->getAmount();
        }
        $purchasePaymentHeader->setTotalAmount($totalAmount);
        $purchaseInvoiceHeader = $purchasePaymentHeader->getPurchaseInvoiceHeader();
        $totalPayment = $purchaseInvoiceHeader->getTotalPayment() + $totalAmount;
        $purchaseInvoiceHeader->setTotalPayment($totalPayment);
        $purchaseInvoiceHeader->setRemaining($purchaseInvoiceHeader->getGrandTotal() - $totalPayment);
    }
    
    public function save(PurchasePaymentHeader $purchasePaymentHeader)
    {
        if (empty($purchasePaymentHeader->getId())) {
            ObjectPersister::save(function() use ($purchasePaymentHeader) {
                $this->purchasePaymentHeaderRepository->add($purchasePaymentHeader, array(
                    'purchasePaymentDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($purchasePaymentHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($purchasePaymentHeader) {
                $this->purchasePaymentHeaderRepository->update($purchasePaymentHeader, array(
                    'purchasePaymentDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($purchasePaymentHeader, true);
            });
        }
    }
    
    public function delete(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $this->beforeDelete($purchasePaymentHeader);
        if (!empty($purchasePaymentHeader->getId())) {
            ObjectPersister::save(function() use ($purchasePaymentHeader) {
                $this->purchasePaymentHeaderRepository->remove($purchasePaymentHeader, array(
                    'purchasePaymentDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($purchasePaymentHeader, false);
            });
        }
    }
    
    protected function beforeDelete(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $purchasePaymentHeader->getPurchasePaymentDetails()->clear();
        $this->sync($purchasePaymentHeader);
    }
    
    private function markJournalLedgers(PurchasePaymentHeader $purchasePaymentHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_PAYABLE_PAYMENT,
            'codeNumberYear' => $purchasePaymentHeader->getCodeNumberYear(),
            'codeNumberMonth' => $purchasePaymentHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $purchasePaymentHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            if ($purchasePaymentDetail->getAmount() > 0) {
                $accountPayableUnit = $this->accountRepository->findPayableUnitRecord();
            
                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($purchasePaymentHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($purchasePaymentHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE_PAYMENT);
                $journalLedgerCredit->setTransactionCategory($purchasePaymentHeader->getPurchaseInvoiceHeader()->getBusinessType());
                $journalLedgerCredit->setTransactionSubject($purchasePaymentDetail->getMemo());
                $journalLedgerCredit->setNote($purchasePaymentHeader->getNote());
                $journalLedgerCredit->setDebit(0);
                $journalLedgerCredit->setCredit($purchasePaymentDetail->getAmount());
                $journalLedgerCredit->setAccount($purchasePaymentDetail->getAccount());
                $journalLedgerCredit->setStaff($purchasePaymentHeader->getStaffFirst());
                $journalLedgerCredit->setPurchaseDeliveryOrder($purchasePaymentHeader->getPurchaseInvoiceHeader()->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerCredit);
            }
        }
        if ($addForHeader) {
            $journalLedgerDebit = new JournalLedger();
            $journalLedgerDebit->setCodeNumber($purchasePaymentHeader->getCodeNumber());
            $journalLedgerDebit->setTransactionDate($purchasePaymentHeader->getTransactionDate());
            $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE_PAYMENT);
            $journalLedgerDebit->setTransactionCategory($purchasePaymentHeader->getPurchaseInvoiceHeader()->getBusinessType());
            $journalLedgerDebit->setTransactionSubject($purchasePaymentHeader->getPurchaseInvoiceHeader()->getSupplier());
            $journalLedgerDebit->setNote($purchasePaymentHeader->getNote());
            $journalLedgerDebit->setDebit($purchasePaymentHeader->getTotalAmount());
            $journalLedgerDebit->setCredit(0);
            $journalLedgerDebit->setAccount($accountPayableUnit);
            $journalLedgerDebit->setStaff($purchasePaymentHeader->getStaffFirst());
            $journalLedgerDebit->setPurchaseDeliveryOrder($purchasePaymentHeader->getPurchaseInvoiceHeader()->getPurchaseDeliveryOrder());
            $this->journalLedgerRepository->add($journalLedgerDebit);
        }
    }
}
