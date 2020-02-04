<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\ReceiveOrder;
use AppBundle\Repository\Transaction\ReceiveOrderRepository;

class ReceiveOrderForm
{
    private $receiveOrderRepository;
    
    public function __construct(ReceiveOrderRepository $receiveOrderRepository)
    {
        $this->receiveOrderRepository = $receiveOrderRepository;
    }
    
    public function initialize(ReceiveOrder $receiveOrder, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($receiveOrder->getId())) {
            $receiveOrder->setStaffFirst($staff);
        }
        $receiveOrder->setStaffLast($staff);
    }
    
    public function finalize(ReceiveOrder $receiveOrder, array $params = array())
    {
        if (empty($receiveOrder->getId())) {
            $transactionDate = $receiveOrder->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastReceiveOrderApplication = $this->receiveOrderRepository->findRecentBy($year, $month);
                $currentReceiveOrder = ($lastReceiveOrderApplication === null) ? $receiveOrder : $lastReceiveOrderApplication;
                $receiveOrder->setCodeNumberToNext($currentReceiveOrder->getCodeNumber(), $year, $month);
            }
        }
        $this->sync($receiveOrder);
    }
    
    private function sync(ReceiveOrder $receiveOrder)
    {
    }
    
    public function save(ReceiveOrder $receiveOrder)
    {
        if (empty($receiveOrder->getId())) {
            ObjectPersister::save(function() use ($receiveOrder) {
                $this->receiveOrderRepository->add($receiveOrder);
            });
        } else {
            ObjectPersister::save(function() use ($receiveOrder) {
                $this->receiveOrderRepository->update($receiveOrder);
            });
        }
    }
    
    public function delete(ReceiveOrder $receiveOrder)
    {
        $this->beforeDelete($receiveOrder);
        if (!empty($receiveOrder->getId())) {
            ObjectPersister::save(function() use ($receiveOrder) {
                $this->receiveOrderRepository->remove($receiveOrder);
            });
        }
    }
    
    protected function beforeDelete(ReceiveOrder $receiveOrder)
    {
        $this->sync($receiveOrder);
    }
}
