<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\ExpenseHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\ExpenseHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;

class ExpenseHeaderForm
{
    private $expenseHeaderRepository;
    private $journalLedgerRepository;
    
    public function __construct(ExpenseHeaderRepository $expenseHeaderRepository, JournalLedgerRepository $journalLedgerRepository)
    {
        $this->expenseHeaderRepository = $expenseHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
    }
    
    public function initialize(ExpenseHeader $expenseHeader, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($expenseHeader->getId())) {
            $expenseHeader->setStaff($staff);
        }
    }
    
    public function finalize(ExpenseHeader $expenseHeader, array $params = array())
    {
        if (empty($expenseHeader->getId())) {
            $transactionDate = $expenseHeader->getTransactionDate();
            if ($transactionDate !== null) {
                $account = $expenseHeader->getAccount();
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastExpenseHeaderApplication = $this->expenseHeaderRepository->findRecentBy($year, $month, $account);
                $currentExpenseHeader = ($lastExpenseHeaderApplication === null) ? $expenseHeader : $lastExpenseHeaderApplication;
                $expenseHeader->setCodeNumberToNext($currentExpenseHeader->getCodeNumber(), $year, $month, $account);
            }
        }
        foreach ($expenseHeader->getExpenseDetails() as $expenseDetail) {
            $expenseDetail->setExpenseHeader($expenseHeader);
        }
    }
    
    public function save(ExpenseHeader $expenseHeader)
    {
        if (empty($expenseHeader->getId())) {
            ObjectPersister::save(function() use ($expenseHeader) {
                $this->expenseHeaderRepository->add($expenseHeader, array(
                    'expenseDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($expenseHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($expenseHeader) {
                $this->expenseHeaderRepository->update($expenseHeader, array(
                    'expenseDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($expenseHeader, true);
            });
        }
    }
    
    public function delete(ExpenseHeader $expenseHeader)
    {
        $this->beforeDelete($expenseHeader);
        if (!empty($expenseHeader->getId())) {
            ObjectPersister::save(function() use ($expenseHeader) {
                $this->expenseHeaderRepository->remove($expenseHeader, array(
                    'expenseDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($expenseHeader, false);
            });
        }
    }
    
    protected function beforeDelete(ExpenseHeader $expenseHeader)
    {
        $expenseHeader->getExpenseDetails()->clear();
    }
    
    private function markJournalLedgers(ExpenseHeader $expenseHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_EXPENSE,
            'codeNumberYear' => $expenseHeader->getCodeNumberYear(),
            'codeNumberMonth' => $expenseHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $expenseHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        foreach ($expenseHeader->getExpenseDetails() as $expenseDetail) {
            if ($expenseDetail->getAmount() > 0) {
                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($expenseHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($expenseHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_EXPENSE);
                $journalLedgerDebit->setTransactionCategory($expenseHeader->getTransactionType());
                $journalLedgerDebit->setTransactionSubject($expenseDetail->getMemo());
                $journalLedgerDebit->setNote($expenseHeader->getNote());
                $journalLedgerDebit->setDebit($expenseDetail->getAmount());
                $journalLedgerDebit->setCredit(0);
                $journalLedgerDebit->setAccount($expenseDetail->getAccount());
                $journalLedgerDebit->setStaff($expenseHeader->getStaff());
                $journalLedgerDebit->setPurchaseDeliveryOrder($expenseHeader->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerDebit);
                
                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($expenseHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($expenseHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_EXPENSE);
                $journalLedgerCredit->setTransactionCategory($expenseHeader->getTransactionType());
                $journalLedgerCredit->setTransactionSubject($expenseDetail->getMemo());
                $journalLedgerCredit->setNote($expenseHeader->getNote());
                $journalLedgerCredit->setDebit(0);
                $journalLedgerCredit->setCredit($expenseDetail->getAmount());
                $journalLedgerCredit->setAccount($expenseHeader->getAccount());
                $journalLedgerCredit->setStaff($expenseHeader->getStaff());
                $journalLedgerCredit->setPurchaseDeliveryOrder($expenseHeader->getPurchaseDeliveryOrder());
                $this->journalLedgerRepository->add($journalLedgerCredit);
            }
        }
    }
}
