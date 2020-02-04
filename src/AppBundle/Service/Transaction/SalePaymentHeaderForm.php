<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SalePaymentHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\SalePaymentHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class SalePaymentHeaderForm
{
    private $salePaymentHeaderRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(SalePaymentHeaderRepository $salePaymentHeaderRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->salePaymentHeaderRepository = $salePaymentHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(SalePaymentHeader $salePaymentHeader, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($salePaymentHeader->getId())) {
            $salePaymentHeader->setStaffFirst($staff);
        }
        $salePaymentHeader->setStaffLast($staff);
    }
    
    public function finalize(SalePaymentHeader $salePaymentHeader, array $params = array())
    {
        if (empty($salePaymentHeader->getId())) {
            $transactionDate = $salePaymentHeader->getTransactionDate();
            $salePaymentDetails = $salePaymentHeader->getSalePaymentDetails();
            if ($transactionDate !== null && count($salePaymentDetails) > 0) {
                $accountCode = $salePaymentDetails[0]->getAccount()->getCode();
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastSalePaymentHeaderApplication = $this->salePaymentHeaderRepository->findRecentBy($year, $month, $accountCode);
                $currentSalePaymentHeader = ($lastSalePaymentHeaderApplication === null) ? $salePaymentHeader : $lastSalePaymentHeaderApplication;
                $salePaymentHeader->setCodeNumberToNext($currentSalePaymentHeader->getCodeNumber(), $year, $month, $accountCode);
            }
        }
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $salePaymentDetail->setSalePaymentHeader($salePaymentHeader);
        }
        $this->sync($salePaymentHeader);
    }
    
    private function sync(SalePaymentHeader $salePaymentHeader)
    {
        $totalAmount = 0.00;
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $totalAmount += $salePaymentDetail->getAmount();
        }
        $salePaymentHeader->setTotalAmount($totalAmount);
        
        $saleInvoiceHeader = $salePaymentHeader->getSaleInvoiceHeader();
        if ($saleInvoiceHeader !== null) {
            $totalPayment = $saleInvoiceHeader->getTotalPayment() + $totalAmount;
            $saleInvoiceHeader->setTotalPayment($totalPayment);
            $saleInvoiceHeader->setRemaining($saleInvoiceHeader->getGrandTotalAfterDownpayment() - $totalPayment);
        }
        
        $saleInvoiceDownpayment = $salePaymentHeader->getSaleInvoiceDownpayment();
        if ($saleInvoiceDownpayment !== null) {
            $totalPayment = $saleInvoiceDownpayment->getTotalPayment() + $totalAmount;
            $saleInvoiceDownpayment->setTotalPayment($totalPayment);
            $saleInvoiceDownpayment->setRemaining($saleInvoiceDownpayment->getAmount() - $totalPayment);
        }
    }
    
    public function save(SalePaymentHeader $salePaymentHeader)
    {
        if (empty($salePaymentHeader->getId())) {
            ObjectPersister::save(function() use ($salePaymentHeader) {
                $this->salePaymentHeaderRepository->add($salePaymentHeader, array(
                    'salePaymentDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($salePaymentHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($salePaymentHeader) {
                $this->salePaymentHeaderRepository->update($salePaymentHeader, array(
                    'salePaymentDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($salePaymentHeader, true);
            });
        }
    }
    
    public function delete(SalePaymentHeader $salePaymentHeader)
    {
        $this->beforeDelete($salePaymentHeader);
        if (!empty($salePaymentHeader->getId())) {
            ObjectPersister::save(function() use ($salePaymentHeader) {
                $this->salePaymentHeaderRepository->remove($salePaymentHeader, array(
                    'salePaymentDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($salePaymentHeader, false);
            });
        }
    }
    
    protected function beforeDelete(SalePaymentHeader $salePaymentHeader)
    {
        $salePaymentHeader->getSalePaymentDetails()->clear();
        $this->sync($salePaymentHeader);
    }
    
    private function markJournalLedgers(SalePaymentHeader $salePaymentHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_EXPENSE,
            'codeNumberYear' => $salePaymentHeader->getCodeNumberYear(),
            'codeNumberMonth' => $salePaymentHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $salePaymentHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        
        $saleInvoice = $salePaymentHeader->getSaleInvoiceHeader();
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            if ($salePaymentDetail->getAmount() > 0) {
                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($salePaymentHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($salePaymentHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE_PAYMENT);
                $journalLedgerDebit->setTransactionCategory(empty($saleInvoice) ? 'unit' : $saleInvoice->getBusinessType());
                $journalLedgerDebit->setTransactionSubject($salePaymentDetail->getMemo());
                $journalLedgerDebit->setNote($salePaymentHeader->getNote());
                $journalLedgerDebit->setDebit($salePaymentDetail->getAmount());
                $journalLedgerDebit->setCredit(0);
                $journalLedgerDebit->setAccount($salePaymentDetail->getAccount());
                $journalLedgerDebit->setStaff($salePaymentHeader->getStaffFirst());
                $journalLedgerDebit->setPurchaseDeliveryOrder(null);
                $this->journalLedgerRepository->add($journalLedgerDebit);
            }
        }
        if ($addForHeader) {
            $accountReceivable = $this->accountRepository->findReceivableRecord();
            
            $journalLedgerCredit = new JournalLedger();
            $journalLedgerCredit->setCodeNumber($salePaymentHeader->getCodeNumber());
            $journalLedgerCredit->setTransactionDate($salePaymentHeader->getTransactionDate());
            $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE_PAYMENT);
            $journalLedgerCredit->setTransactionCategory(empty($saleInvoice) ? 'unit' : $saleInvoice->getBusinessType());
            $journalLedgerCredit->setTransactionSubject(empty($saleInvoice) ? $salePaymentHeader->getSaleInvoiceDownpayment()->getCustomer() : $saleInvoice->getCustomer());
            $journalLedgerCredit->setNote($salePaymentHeader->getNote());
            $journalLedgerCredit->setDebit(0);
            $journalLedgerCredit->setCredit($salePaymentHeader->getTotalAmount());
            $journalLedgerCredit->setAccount($accountReceivable);
            $journalLedgerCredit->setStaff($salePaymentHeader->getStaffFirst());
            $journalLedgerCredit->setPurchaseDeliveryOrder(null);
            $this->journalLedgerRepository->add($journalLedgerCredit);
        }
    }
}
