<?php

namespace AppBundle\Repository\Transaction;

use LibBundle\Doctrine\EntityRepository;

class DepositHeaderRepository extends EntityRepository
{
    public function findRecentBy($year, $month, $account)
    {
        $query = $this->_em->createQuery('SELECT t FROM AppBundle\Entity\Transaction\DepositHeader t WHERE t.codeNumberMonth = :codeNumberMonth AND t.codeNumberYear = :codeNumberYear AND t.account = :account ORDER BY t.codeNumberOrdinal DESC');
        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setParameter('account', $account);
        $query->setMaxResults(1);
        $lastDepositHeader = $query->getOneOrNullResult();
        
        return $lastDepositHeader;
    }
}
