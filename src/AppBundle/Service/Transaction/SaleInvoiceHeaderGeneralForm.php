<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\SaleInvoiceHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class SaleInvoiceHeaderGeneralForm
{
    private $saleInvoiceHeaderRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->saleInvoiceHeaderRepository = $saleInvoiceHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(SaleInvoiceHeader $saleInvoiceHeader, array $params = array())
    {
        list($date, $staff) = array($params['date'], $params['staff']);
        
        if (empty($saleInvoiceHeader->getId())) {
            $createdDate = date_create_from_format('Y-m-d', $date);
            $saleInvoiceHeader->setCreatedDate($createdDate);
            $saleInvoiceHeader->setBusinessType(SaleInvoiceHeader::BUSINESS_TYPE_GENERAL);
            $saleInvoiceHeader->setStaffFirst($staff);
        }
        $saleInvoiceHeader->setStaffLast($staff);
    }
    
    public function finalize(SaleInvoiceHeader $saleInvoiceHeader, array $params = array())
    {
        if (empty($saleInvoiceHeader->getId())) {
            $transactionDate = $saleInvoiceHeader->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastSaleInvoiceHeaderApplication = $this->saleInvoiceHeaderRepository->findRecentBy($year, $month);
                $currentSaleInvoiceHeader = ($lastSaleInvoiceHeaderApplication === null) ? $saleInvoiceHeader : $lastSaleInvoiceHeaderApplication;
                $saleInvoiceHeader->setCodeNumberToNext($currentSaleInvoiceHeader->getCodeNumber(), $year, $month);
            }
        }
        foreach ($saleInvoiceHeader->getSaleInvoiceDetailGenerals() as $saleInvoiceDetailGeneral) {
            $saleInvoiceDetailGeneral->setSaleInvoiceHeader($saleInvoiceHeader);
        }
        $this->sync($saleInvoiceHeader);
    }
    
    private function sync(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $customer = $saleInvoiceHeader->getCustomer();
        $transactionDate = $saleInvoiceHeader->getTransactionDate();
        if ($transactionDate !== null && $customer !== null) {
            $creditPaymentTerm = $customer->getCreditPaymentTerm();
            $saleInvoiceHeader->setDueDate($transactionDate->add(date_interval_create_from_date_string("{$creditPaymentTerm} days")));
        }
        $saleInvoiceHeader->sync();
    }
    
    public function save(SaleInvoiceHeader $saleInvoiceHeader)
    {
        if (empty($saleInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($saleInvoiceHeader) {
                $this->saleInvoiceHeaderRepository->add($saleInvoiceHeader, array(
                    'saleInvoiceDetailGenerals' => array('add' => true),
                ));
                $this->markJournalLedgers($saleInvoiceHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($saleInvoiceHeader) {
                $this->saleInvoiceHeaderRepository->update($saleInvoiceHeader, array(
                    'saleInvoiceDetailGenerals' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($saleInvoiceHeader, true);
            });
        }
    }
    
    public function delete(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $this->beforeDelete($saleInvoiceHeader);
        if (!empty($saleInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($saleInvoiceHeader) {
                $this->saleInvoiceHeaderRepository->remove($saleInvoiceHeader, array(
                    'saleInvoiceDetailGenerals' => array('remove' => true),
                ));
                $this->markJournalLedgers($saleInvoiceHeader, false);
            });
        }
    }
    
    protected function beforeDelete(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeader->getSaleInvoiceDetailGenerals()->clear();
        $this->sync($saleInvoiceHeader);
    }
    
    private function markJournalLedgers(SaleInvoiceHeader $saleInvoiceHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_RECEIVABLE,
            'codeNumberYear' => $saleInvoiceHeader->getCodeNumberYear(),
            'codeNumberMonth' => $saleInvoiceHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $saleInvoiceHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        if ($addForHeader && $saleInvoiceHeader->getGrandTotalBeforeDownpayment() > 0) {
            $accountReceivable = $this->accountRepository->findReceivableRecord();
            $accountSaleUnit = $this->accountRepository->findSaleUnitRecord();
            
            $journalLedgerDebit = new JournalLedger();
            $journalLedgerDebit->setCodeNumber($saleInvoiceHeader->getCodeNumber());
            $journalLedgerDebit->setTransactionDate($saleInvoiceHeader->getTransactionDate());
            $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE);
            $journalLedgerDebit->setTransactionCategory(SaleInvoiceHeader::BUSINESS_TYPE_GENERAL);
            $journalLedgerDebit->setTransactionSubject($saleInvoiceHeader->getCustomer());
            $journalLedgerDebit->setNote($saleInvoiceHeader->getNote());
            $journalLedgerDebit->setDebit($saleInvoiceHeader->getGrandTotalBeforeDownpayment());
            $journalLedgerDebit->setCredit(0);
            $journalLedgerDebit->setAccount($accountReceivable);
            $journalLedgerDebit->setStaff($saleInvoiceHeader->getStaffFirst());
            $journalLedgerDebit->setPurchaseDeliveryOrder(null);
            $this->journalLedgerRepository->add($journalLedgerDebit);

            $journalLedgerCredit = new JournalLedger();
            $journalLedgerCredit->setCodeNumber($saleInvoiceHeader->getCodeNumber());
            $journalLedgerCredit->setTransactionDate($saleInvoiceHeader->getTransactionDate());
            $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE);
            $journalLedgerCredit->setTransactionCategory(SaleInvoiceHeader::BUSINESS_TYPE_GENERAL);
            $journalLedgerCredit->setTransactionSubject($saleInvoiceHeader->getCustomer());
            $journalLedgerCredit->setNote($saleInvoiceHeader->getNote());
            $journalLedgerCredit->setDebit(0);
            $journalLedgerCredit->setCredit($saleInvoiceHeader->getGrandTotalBeforeDownpayment());
            $journalLedgerCredit->setAccount($accountSaleUnit);
            $journalLedgerCredit->setStaff($saleInvoiceHeader->getStaffFirst());
            $journalLedgerCredit->setPurchaseDeliveryOrder(null);
            $this->journalLedgerRepository->add($journalLedgerCredit);
        }
    }
}
