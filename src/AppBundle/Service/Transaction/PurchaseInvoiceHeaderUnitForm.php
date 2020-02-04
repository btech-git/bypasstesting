<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class PurchaseInvoiceHeaderUnitForm
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
            $purchaseInvoiceHeader->setBusinessType(PurchaseInvoiceHeader::BUSINESS_TYPE_UNIT);
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
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetailUnits() as $purchaseInvoiceDetailUnit) {
            $purchaseInvoiceDetailUnit->setPurchaseInvoiceHeader($purchaseInvoiceHeader);
        }
        $this->sync($purchaseInvoiceHeader);
    }
    
    private function sync(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetailUnits() as $purchaseInvoiceDetailUnit) {
            $purchaseInvoiceDetailUnit->setVehicleChassisNumber($purchaseInvoiceDetailUnit->getPurchaseDeliveryOrder()->getVehicleChassisNumber());
            $purchaseInvoiceDetailUnit->setVehicleMachineNumber($purchaseInvoiceDetailUnit->getPurchaseDeliveryOrder()->getVehicleMachineNumber());
            $purchaseInvoiceDetailUnit->setQuantity('1');
        }
        $purchaseInvoiceHeader->sync();
    }
    
    public function save(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        if (empty($purchaseInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetailUnits' => array('add' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->update($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetailUnits' => array('add' => true, 'remove' => true),
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
                    'purchaseInvoiceDetailUnits' => array('remove' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, false);
            });
        }
    }
    
    protected function beforeDelete(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeader->getPurchaseInvoiceDetailUnits()->clear();
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
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetailUnits() as $purchaseInvoiceDetail) {
            if ($addForHeader && $purchaseInvoiceHeader->getGrandTotal() > 0) {
                $accountInventoryUnit = $this->accountRepository->findInventoryUnitRecord();
                $accountPayableUnit = $this->accountRepository->findPayableUnitRecord();

                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($purchaseInvoiceHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($purchaseInvoiceHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE);
                $journalLedgerDebit->setTransactionCategory($purchaseInvoiceHeader::BUSINESS_TYPE_UNIT);
                $journalLedgerDebit->setTransactionSubject($purchaseInvoiceHeader->getSupplier());
                $journalLedgerDebit->setNote($purchaseInvoiceHeader->getNote());
                $journalLedgerDebit->setDebit($purchaseInvoiceDetail->getTotal());
                $journalLedgerDebit->setCredit(0);
                $journalLedgerDebit->setAccount($accountInventoryUnit);
                $journalLedgerDebit->setStaff($purchaseInvoiceHeader->getStaffFirst());
                $journalLedgerDebit->setPurchaseDeliveryOrder($purchaseInvoiceDetail->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerDebit);

                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($purchaseInvoiceHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($purchaseInvoiceHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE);
                $journalLedgerCredit->setTransactionCategory($purchaseInvoiceHeader::BUSINESS_TYPE_UNIT);
                $journalLedgerCredit->setTransactionSubject($purchaseInvoiceHeader->getSupplier());
                $journalLedgerCredit->setNote($purchaseInvoiceHeader->getNote());
                $journalLedgerCredit->setDebit(0);
                $journalLedgerCredit->setCredit($purchaseInvoiceDetail->getTotal());
                $journalLedgerCredit->setAccount($accountPayableUnit);
                $journalLedgerCredit->setStaff($purchaseInvoiceHeader->getStaffFirst());
                $journalLedgerCredit->setPurchaseDeliveryOrder($purchaseInvoiceDetail->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerCredit);
            }
        }
    }
}
