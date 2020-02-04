<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\SaleInvoiceHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class SaleInvoiceHeaderUnitForm
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
            $saleInvoiceHeader->setBusinessType(SaleInvoiceHeader::BUSINESS_TYPE_UNIT);
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
        foreach ($saleInvoiceHeader->getSaleInvoiceDetailUnits() as $saleInvoiceDetailUnit) {
            $saleInvoiceDetailUnit->setSaleInvoiceHeader($saleInvoiceHeader);
        }
        foreach ($saleInvoiceHeader->getSaleInvoiceDetailUnitDownpayments() as $saleInvoiceDetailUnitDownpayment) {
            $saleInvoiceDetailUnitDownpayment->setSaleInvoiceHeader($saleInvoiceHeader);
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
        foreach ($saleInvoiceHeader->getSaleInvoiceDetailUnits() as $saleInvoiceDetailUnit) {
            $purchaseDeliveryOrder = $saleInvoiceDetailUnit->getReceiveOrder()->getPurchaseDeliveryOrder();
            $saleInvoiceDetailUnit->setVehicleChassisNumber($purchaseDeliveryOrder->getVehicleChassisNumber());
            $saleInvoiceDetailUnit->setVehicleMachineNumber($purchaseDeliveryOrder->getVehicleMachineNumber());
            $saleInvoiceDetailUnit->setVehicleModel($purchaseDeliveryOrder->getVehicleModel());
            $saleInvoiceDetailUnit->setQuantity(1);
            $saleInvoiceDetailUnit->setUnitPrice($purchaseDeliveryOrder->getSaleOrder()->getUnitPrice());
            $saleInvoiceDetailUnit->setStaffSalesman($purchaseDeliveryOrder->getSaleOrder()->getStaffFirst());
        }
        foreach ($saleInvoiceHeader->getSaleInvoiceDetailUnitDownpayments() as $saleInvoiceDetailUnitDownpayment) {
            $saleInvoiceDetailUnitDownpayment->setAmount($saleInvoiceDetailUnitDownpayment->getSaleInvoiceDownpayment()->getAmount());
        }
        $saleInvoiceHeader->sync();
    }
    
    public function save(SaleInvoiceHeader $saleInvoiceHeader)
    {
        if (empty($saleInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($saleInvoiceHeader) {
                $this->saleInvoiceHeaderRepository->add($saleInvoiceHeader, array(
                    'saleInvoiceDetailUnits' => array('add' => true),
                    'saleInvoiceDetailUnitDownpayments' => array('add' => true),
                ));
                $this->markJournalLedgers($saleInvoiceHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($saleInvoiceHeader) {
                $this->saleInvoiceHeaderRepository->update($saleInvoiceHeader, array(
                    'saleInvoiceDetailUnits' => array('add' => true, 'remove' => true),
                    'saleInvoiceDetailUnitDownpayments' => array('add' => true, 'remove' => true),
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
                    'saleInvoiceDetailUnits' => array('remove' => true),
                    'saleInvoiceDetailUnitDownpayments' => array('remove' => true),
                ));
                $this->markJournalLedgers($saleInvoiceHeader, false);
            });
        }
    }
    
    protected function beforeDelete(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeader->getSaleInvoiceDetailUnits()->clear();
        $saleInvoiceHeader->getSaleInvoiceDetailUnitDownpayments()->clear();
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
        foreach ($saleInvoiceHeader->getSaleInvoiceDetailUnits() as $saleInvoiceDetail) {
            if ($addForHeader && $saleInvoiceHeader->getGrandTotalAfterDownpayment() > 0) {
                $accountReceivable = $this->accountRepository->findReceivableRecord();
                $accountSaleUnit = $this->accountRepository->findSaleUnitRecord();

                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($saleInvoiceHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($saleInvoiceHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE);
                $journalLedgerDebit->setTransactionCategory(SaleInvoiceHeader::BUSINESS_TYPE_UNIT);
                $journalLedgerDebit->setTransactionSubject($saleInvoiceHeader->getCustomer());
                $journalLedgerDebit->setNote($saleInvoiceHeader->getNote());
                $journalLedgerDebit->setDebit($saleInvoiceDetail->getTotal());
                $journalLedgerDebit->setCredit(0);
                $journalLedgerDebit->setAccount($accountReceivable);
                $journalLedgerDebit->setStaff($saleInvoiceHeader->getStaffFirst());
                $journalLedgerDebit->setPurchaseDeliveryOrder($saleInvoiceDetail->getReceiveOrder()->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerDebit);

                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($saleInvoiceHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($saleInvoiceHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE);
                $journalLedgerCredit->setTransactionCategory(SaleInvoiceHeader::BUSINESS_TYPE_UNIT);
                $journalLedgerCredit->setTransactionSubject($saleInvoiceHeader->getCustomer());
                $journalLedgerCredit->setNote($saleInvoiceHeader->getNote());
                $journalLedgerCredit->setDebit(0);
                $journalLedgerCredit->setCredit($saleInvoiceDetail->getTotal());
                $journalLedgerCredit->setAccount($accountSaleUnit);
                $journalLedgerCredit->setStaff($saleInvoiceHeader->getStaffFirst());
                $journalLedgerCredit->setPurchaseDeliveryOrder($saleInvoiceDetail->getReceiveOrder()->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerCredit);
            }
        }
    }
}
