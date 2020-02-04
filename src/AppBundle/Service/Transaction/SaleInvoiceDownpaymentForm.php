<?php

namespace AppBundle\Service\Transaction;

use Doctrine\Common\Collections\ArrayCollection;
use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\SaleInvoiceDownpaymentRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class SaleInvoiceDownpaymentForm
{
    private $saleInvoiceDownpaymentRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(SaleInvoiceDownpaymentRepository $saleInvoiceDownpaymentRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->saleInvoiceDownpaymentRepository = $saleInvoiceDownpaymentRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(SaleInvoiceDownpayment $saleInvoiceDownpayment, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($saleInvoiceDownpayment->getId())) {
            $saleInvoiceDownpayment->setStaffFirst($staff);
        }
        $saleInvoiceDownpayment->setStaffLast($staff);
    }
    
    public function finalize(SaleInvoiceDownpayment $saleInvoiceDownpayment, array $params = array())
    {
        if (empty($saleInvoiceDownpayment->getId())) {
            $transactionDate = $saleInvoiceDownpayment->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastSaleInvoiceDownpaymentApplication = $this->saleInvoiceDownpaymentRepository->findRecentBy($year, $month);
                $currentSaleInvoiceDownpayment = ($lastSaleInvoiceDownpaymentApplication === null) ? $saleInvoiceDownpayment : $lastSaleInvoiceDownpaymentApplication;
                $saleInvoiceDownpayment->setCodeNumberToNext($currentSaleInvoiceDownpayment->getCodeNumber(), $year, $month);
            }
        }
        
        $this->sync($saleInvoiceDownpayment, false);
    }
    
    private function sync(SaleInvoiceDownpayment $saleInvoiceDownpayment, $mergeForDelete)
    {
        $saleOrder = $saleInvoiceDownpayment->getSaleOrder();
        if ($saleOrder !== null) {
            $saleInvoiceDownpayment->setCustomer($saleOrder->getCustomer());
        }
        $oldSaleInvoiceDownpayments = $saleOrder->getSaleInvoiceDownpayments();
        $saleInvoiceDownpaymentList = new ArrayCollection($oldSaleInvoiceDownpayments->getValues());
        if ($mergeForDelete) {
            $saleInvoiceDownpaymentList->removeElement($saleInvoiceDownpayment);
        } else if (empty($saleInvoiceDownpayment->getId())) {
            $saleInvoiceDownpaymentList->add($saleInvoiceDownpayment);
        }
        $totalAmount = 0.00;
        foreach ($saleInvoiceDownpaymentList as $saleInvoiceDownpaymentItem) {
            $totalAmount += $saleInvoiceDownpaymentItem->getAmount();
        }
        $saleOrder->setDownpaymentRemaining($saleOrder->getDownPayment() - $totalAmount);
        
        $saleInvoiceDownpayment->sync();
    }
    
    public function save(SaleInvoiceDownpayment $saleInvoiceDownpayment)
    {
        if (empty($saleInvoiceDownpayment->getId())) {
            ObjectPersister::save(function() use ($saleInvoiceDownpayment) {
                $this->saleInvoiceDownpaymentRepository->add($saleInvoiceDownpayment);
                $this->markJournalLedgers($saleInvoiceDownpayment, true);
            });
        } else {
            ObjectPersister::save(function() use ($saleInvoiceDownpayment) {
                $this->saleInvoiceDownpaymentRepository->update($saleInvoiceDownpayment);
                $this->markJournalLedgers($saleInvoiceDownpayment, true);
            });
        }
    }
    
    public function delete(SaleInvoiceDownpayment $saleInvoiceDownpayment)
    {
        $this->beforeDelete($saleInvoiceDownpayment);
        if (!empty($saleInvoiceDownpayment->getId())) {
            ObjectPersister::save(function() use ($saleInvoiceDownpayment) {
                $this->saleInvoiceDownpaymentRepository->remove($saleInvoiceDownpayment);
                $this->markJournalLedgers($saleInvoiceDownpayment, false);
            });
        }
    }
    
    protected function beforeDelete(SaleInvoiceDownpayment $saleInvoiceDownpayment)
    {
        $this->sync($saleInvoiceDownpayment, true);
    }
    
    private function markJournalLedgers(SaleInvoiceDownpayment $saleInvoiceDownpayment, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_SALE_DOWNPAYMENT,
            'codeNumberYear' => $saleInvoiceDownpayment->getCodeNumberYear(),
            'codeNumberMonth' => $saleInvoiceDownpayment->getCodeNumberMonth(),
            'codeNumberOrdinal' => $saleInvoiceDownpayment->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        
        if ($addForHeader && $saleInvoiceDownpayment->getAmount() > 0) {
            $accountSaleUnit = $this->accountRepository->findSaleUnitRecord();
            $accountReceivable = $this->accountRepository->findReceivableRecord();
            
            $journalLedgerDebit = new JournalLedger();
            $journalLedgerDebit->setCodeNumber($saleInvoiceDownpayment->getCodeNumber());
            $journalLedgerDebit->setTransactionDate($saleInvoiceDownpayment->getTransactionDate());
            $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_SALE_DOWNPAYMENT);
            $journalLedgerDebit->setTransactionCategory(JournalLedger::TRANSACTION_CATEGORY_UNIT);
            $journalLedgerDebit->setTransactionSubject($saleInvoiceDownpayment->getCustomer());
            $journalLedgerDebit->setNote($saleInvoiceDownpayment->getNote());
            $journalLedgerDebit->setDebit($saleInvoiceDownpayment->getAmount());
            $journalLedgerDebit->setCredit(0.00);
            $journalLedgerDebit->setAccount($accountReceivable);
            $journalLedgerDebit->setStaff($saleInvoiceDownpayment->getStaffFirst());
            $journalLedgerDebit->setPurchaseDeliveryOrder(null);
            $this->journalLedgerRepository->add($journalLedgerDebit);

            $journalLedgerCredit = new JournalLedger();
            $journalLedgerCredit->setCodeNumber($saleInvoiceDownpayment->getCodeNumber());
            $journalLedgerCredit->setTransactionDate($saleInvoiceDownpayment->getTransactionDate());
            $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_SALE_DOWNPAYMENT);
            $journalLedgerCredit->setTransactionCategory(JournalLedger::TRANSACTION_CATEGORY_UNIT);
            $journalLedgerCredit->setTransactionSubject($saleInvoiceDownpayment->getCustomer());
            $journalLedgerCredit->setNote($saleInvoiceDownpayment->getNote());
            $journalLedgerCredit->setDebit(0.00);
            $journalLedgerCredit->setCredit($saleInvoiceDownpayment->getAmount());
            $journalLedgerCredit->setAccount($accountSaleUnit);
            $journalLedgerCredit->setStaff($saleInvoiceDownpayment->getStaffFirst());
            $journalLedgerCredit->setPurchaseDeliveryOrder(null);
            $this->journalLedgerRepository->add($journalLedgerCredit);
        }
    }
}
