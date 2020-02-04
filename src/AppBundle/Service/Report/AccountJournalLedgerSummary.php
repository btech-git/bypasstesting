<?php

namespace AppBundle\Service\Report;

use LibBundle\Grid\DataGridView;
use AppBundle\Repository\Report\JournalLedgerRepository;

class AccountJournalLedgerSummary
{
    private $journalLedgerRepository;
    
    public function __construct(JournalLedgerRepository $journalLedgerRepository)
    {
        $this->journalLedgerRepository = $journalLedgerRepository;
    }
    
    public function getBeginningBalanceData(DataGridView $dataGridView)
    {
        $startDate = $dataGridView->searchVals['journalLedgers']['transactionDate'][1];
        $beginningBalanceSummary = array();
        foreach ($dataGridView->data as $i => $account) {
            $beginningBalance = $this->journalLedgerRepository->getBeginningBalance($account, $startDate);
            $beginningBalanceSummary[$i] = $beginningBalance;
        }
        return $beginningBalanceSummary;
    }
}
