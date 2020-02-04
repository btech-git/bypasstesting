<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\JournalVoucherHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\JournalVoucherHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;

class JournalVoucherHeaderForm
{
    private $journalVoucherHeaderRepository;
    private $journalLedgerRepository;
    
    public function __construct(JournalVoucherHeaderRepository $journalVoucherHeaderRepository, JournalLedgerRepository $journalLedgerRepository)
    {
        $this->journalVoucherHeaderRepository = $journalVoucherHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
    }
    
    public function initialize(JournalVoucherHeader $journalVoucherHeader, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($journalVoucherHeader->getId())) {
            $journalVoucherHeader->setStaff($staff);
        }
    }
    
    public function finalize(JournalVoucherHeader $journalVoucherHeader, array $params = array())
    {
        if (empty($journalVoucherHeader->getId())) {
            $transactionDate = $journalVoucherHeader->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastJournalVoucherHeaderApplication = $this->journalVoucherHeaderRepository->findRecentBy($year, $month);
                $currentJournalVoucherHeader = ($lastJournalVoucherHeaderApplication === null) ? $journalVoucherHeader : $lastJournalVoucherHeaderApplication;
                $journalVoucherHeader->setCodeNumberToNext($currentJournalVoucherHeader->getCodeNumber(), $year, $month);
            }
        }
        foreach ($journalVoucherHeader->getJournalVoucherDetails() as $journalVoucherDetail) {
            $journalVoucherDetail->setJournalVoucherHeader($journalVoucherHeader);
        }
    }
    
    public function save(JournalVoucherHeader $journalVoucherHeader)
    {
        if (empty($journalVoucherHeader->getId())) {
            ObjectPersister::save(function() use ($journalVoucherHeader) {
                $this->journalVoucherHeaderRepository->add($journalVoucherHeader, array(
                    'journalVoucherDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($journalVoucherHeader);
            });
        } else {
            ObjectPersister::save(function() use ($journalVoucherHeader) {
                $this->journalVoucherHeaderRepository->update($journalVoucherHeader, array(
                    'journalVoucherDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($journalVoucherHeader);
            });
        }
    }
    
    public function delete(JournalVoucherHeader $journalVoucherHeader)
    {
        $this->beforeDelete($journalVoucherHeader);
        if (!empty($journalVoucherHeader->getId())) {
            ObjectPersister::save(function() use ($journalVoucherHeader) {
                $this->journalVoucherHeaderRepository->remove($journalVoucherHeader, array(
                    'journalVoucherDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($journalVoucherHeader);
            });
        }
    }
    
    protected function beforeDelete(JournalVoucherHeader $journalVoucherHeader)
    {
        $journalVoucherHeader->getJournalVoucherDetails()->clear();
        $this->sync($journalVoucherHeader);
    }
    
    private function markJournalLedgers(JournalVoucherHeader $journalVoucherHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_VOUCHER,
            'codeNumberYear' => $journalVoucherHeader->getCodeNumberYear(),
            'codeNumberMonth' => $journalVoucherHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $journalVoucherHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        foreach ($journalVoucherHeader->getJournalVoucherDetails() as $journalVoucherDetail) {
            if ($journalVoucherDetail->getDebit() > 0 || $journalVoucherDetail->getCredit() > 0) {
                $journalLedger = new JournalLedger();
                $journalLedger->setCodeNumber($journalVoucherHeader->getCodeNumber());
                $journalLedger->setTransactionDate($journalVoucherHeader->getTransactionDate());
                $journalLedger->setTransactionType(JournalLedger::TRANSACTION_TYPE_VOUCHER);
                $journalLedger->setTransactionCategory($journalVoucherHeader->getTransactionType());
                $journalLedger->setTransactionSubject($journalVoucherDetail->getMemo());
                $journalLedger->setNote($journalVoucherHeader->getNote());
                $journalLedger->setDebit($journalVoucherDetail->getDebit());
                $journalLedger->setCredit($journalVoucherDetail->getCredit());
                $journalLedger->setAccount($journalVoucherDetail->getAccount());
                $journalLedger->setStaff($journalVoucherHeader->getStaff());
                $journalLedger->setPurchaseDeliveryOrder($journalVoucherHeader->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedger);
            }
        }
    }
}
