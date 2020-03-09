<?php

namespace AppBundle\Repository\Transaction;

use LibBundle\Doctrine\EntityRepository;

class ExpenseHeaderRepository extends EntityRepository
{
    public function findRecentBy($year, $month, $account)
    {
        $query = $this->_em->createQuery('SELECT t FROM AppBundle\Entity\Transaction\ExpenseHeader t WHERE t.codeNumberMonth = :codeNumberMonth AND t.codeNumberYear = :codeNumberYear AND t.account = :account ORDER BY t.codeNumberOrdinal DESC');
        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setParameter('account', $account);
        $query->setMaxResults(1);
        $lastExpenseHeader = $query->getOneOrNullResult();
        
        return $lastExpenseHeader;
    }
}
