<?php

namespace AppBundle\Repository\Report;

use LibBundle\Doctrine\EntityRepository;

class JournalLedgerRepository extends EntityRepository
{
    public function getBeginningBalance($account, $startDate)
    {
        $query = $this->_em->createQuery('SELECT COALESCE(SUM(t.debit - t.credit), 0) AS beginningBalance FROM AppBundle\Entity\Report\JournalLedger t WHERE t.account = :account AND t.transactionDate < :transactionDate');
        $query->setParameter('account', $account);
        $query->setParameter('transactionDate', $startDate);
        $beginningBalance = $query->getSingleScalarResult();
        
        return $beginningBalance;
    }
    
    public function getProfitLossData($endDate)
    {
        $query = $this->_em->createQuery('
            SELECT a.code AS account_code, c.code AS account_category_code, a.name AS account_name, c.name AS account_category_name, SUM(j.credit - j.debit) AS total
            FROM AppBundle\Entity\Report\JournalLedger j
            JOIN j.account a
            JOIN a.accountCategory c
            WHERE a.isProfitLoss = true AND j.transactionDate <= :transactionDate
            GROUP BY a.id
            ORDER BY c.code ASC, a.code ASC
        ');
        $query->setParameter('transactionDate', $endDate);
        $profitLossData = $query->getResult();
        
        return $profitLossData;
    }
    
    public function getBalanceSheetData($endDate)
    {
        $query = $this->_em->createQuery('
            SELECT a.code AS account_code, c.code AS account_category_code, a.name AS account_name, c.name AS account_category_name, SUM(j.debit - j.credit) AS total
            FROM AppBundle\Entity\Report\JournalLedger j
            JOIN j.account a
            JOIN a.accountCategory c
            WHERE a.isProfitLoss = false AND j.transactionDate <= :transactionDate
            GROUP BY a.id
            ORDER BY c.code ASC, a.code ASC
        ');
        $query->setParameter('transactionDate', $endDate);
        $profitLossData = $query->getResult();
        
        return $profitLossData;
    }
}
