<?php

namespace AppBundle\Repository\Transaction;

use LibBundle\Doctrine\EntityRepository;

class PurchaseDeliveryOrderRepository extends EntityRepository
{
    public function findRecentBy($year, $month)
    {
        $query = $this->_em->createQuery('SELECT t FROM AppBundle\Entity\Transaction\PurchaseDeliveryOrder t WHERE t.codeNumberMonth = :codeNumberMonth AND t.codeNumberYear = :codeNumberYear ORDER BY t.codeNumberOrdinal DESC');
        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastPurchaseDeliveryOrder = $query->getOneOrNullResult();
        
        return $lastPurchaseDeliveryOrder;
    }

    public function findOutstandingsBy($size, $offset)
    {
        $query = $this->_em->createQuery('SELECT t FROM AppBundle\Entity\Transaction\PurchaseDeliveryOrder t WHERE DATE_DIFF(CURRENT_DATE(), t.transactionDate) >= 6 AND t.id NOT IN (SELECT IDENTITY(r.purchaseDeliveryOrder) FROM AppBundle\Entity\Transaction\ReceiveOrder r)');
        $query->setMaxResults($size);
        $query->setFirstResult($offset);
        $purchaseDeliveryOrders = $query->getResult();
        
        return $purchaseDeliveryOrders;
    }

    public function countOutstandings()
    {
        $query = $this->_em->createQuery('SELECT COUNT(t) FROM AppBundle\Entity\Transaction\PurchaseDeliveryOrder t WHERE DATE_DIFF(CURRENT_DATE(), t.transactionDate) >= 6 AND t.id NOT IN (SELECT IDENTITY(r.purchaseDeliveryOrder) FROM AppBundle\Entity\Transaction\ReceiveOrder r)');
        $count = $query->getSingleScalarResult();
        
        return $count;
    }
}
