<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\DepositHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\DepositHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;

class DepositHeaderForm
{
    private $depositHeaderRepository;
    private $journalLedgerRepository;
    
    public function __construct(DepositHeaderRepository $depositHeaderRepository, JournalLedgerRepository $journalLedgerRepository)
    {
        $this->depositHeaderRepository = $depositHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
    }
    
    public function initialize(DepositHeader $depositHeader, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($depositHeader->getId())) {
            $depositHeader->setStaff($staff);
        }
    }
    
    public function finalize(DepositHeader $depositHeader, array $params = array())
    {
        if (empty($depositHeader->getId())) {
            $transactionDate = $depositHeader->getTransactionDate();
            if ($transactionDate !== null) {
                $account = $depositHeader->getAccount();
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastDepositHeaderApplication = $this->depositHeaderRepository->findRecentBy($year, $month, $account);
                $currentDepositHeader = ($lastDepositHeaderApplication === null) ? $depositHeader : $lastDepositHeaderApplication;
                $depositHeader->setCodeNumberToNext($currentDepositHeader->getCodeNumber(), $year, $month, $account);
            }
        }
        foreach ($depositHeader->getDepositDetails() as $depositDetail) {
            $depositDetail->setDepositHeader($depositHeader);
        }
    }
    
    public function save(DepositHeader $depositHeader)
    {
        if (empty($depositHeader->getId())) {
            ObjectPersister::save(function() use ($depositHeader) {
                $this->depositHeaderRepository->add($depositHeader, array(
                    'depositDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($depositHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($depositHeader) {
                $this->depositHeaderRepository->update($depositHeader, array(
                    'depositDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($depositHeader, true);
            });
        }
    }
    
    public function delete(DepositHeader $depositHeader)
    {
        $this->beforeDelete($depositHeader);
        if (!empty($depositHeader->getId())) {
            ObjectPersister::save(function() use ($depositHeader) {
                $this->depositHeaderRepository->remove($depositHeader, array(
                    'depositDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($depositHeader, false);
            });
        }
    }
    
    protected function beforeDelete(DepositHeader $depositHeader)
    {
        $depositHeader->getDepositDetails()->clear();
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
                $journalLedgerCredit->setTransactionCategory($depositHeader->getTransactionType());
                $journalLedgerCredit->setTransactionSubject($depositDetail->getMemo());
                $journalLedgerCredit->setNote($depositHeader->getNote());
                $journalLedgerCredit->setDebit(0.00);
                $journalLedgerCredit->setCredit($depositDetail->getAmount());
                $journalLedgerCredit->setAccount($depositDetail->getAccount());
                $journalLedgerCredit->setStaff($depositHeader->getStaff());
                $journalLedgerCredit->setPurchaseDeliveryOrder($depositHeader->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerCredit);
                
                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($depositHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($depositHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_DEPOSIT);
                $journalLedgerDebit->setTransactionCategory($depositHeader->getTransactionType());
                $journalLedgerDebit->setTransactionSubject($depositDetail->getMemo());
                $journalLedgerDebit->setNote($depositHeader->getNote());
                $journalLedgerDebit->setDebit($depositDetail->getAmount());
                $journalLedgerDebit->setCredit(0.00);
                $journalLedgerDebit->setAccount($depositHeader->getAccount());
                $journalLedgerDebit->setStaff($depositHeader->getStaff());
                $journalLedgerDebit->setPurchaseDeliveryOrder($depositHeader->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerDebit);
            }
        }
    }
}
