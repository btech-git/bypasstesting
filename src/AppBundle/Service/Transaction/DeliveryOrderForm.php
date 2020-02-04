<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\DeliveryOrder;
use AppBundle\Repository\Transaction\DeliveryOrderRepository;

class DeliveryOrderForm
{
    private $deliveryOrderRepository;
    
    public function __construct(DeliveryOrderRepository $deliveryOrderRepository)
    {
        $this->deliveryOrderRepository = $deliveryOrderRepository;
    }
    
    public function initialize(DeliveryOrder $deliveryOrder, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($deliveryOrder->getId())) {
            $deliveryOrder->setStaffFirst($staff);
        }
        $deliveryOrder->setStaffLast($staff);
    }
    
    public function finalize(DeliveryOrder $deliveryOrder, array $params = array())
    {
        if (empty($deliveryOrder->getId())) {
            $transactionDate = $deliveryOrder->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastDeliveryOrderApplication = $this->deliveryOrderRepository->findRecentBy($year, $month);
                $currentDeliveryOrder = ($lastDeliveryOrderApplication === null) ? $deliveryOrder : $lastDeliveryOrderApplication;
                $deliveryOrder->setCodeNumberToNext($currentDeliveryOrder->getCodeNumber(), $year, $month);
            }
        }
        $this->sync($deliveryOrder);
    }
    
    private function sync(DeliveryOrder $deliveryOrder)
    {
    }
    
    public function save(DeliveryOrder $deliveryOrder)
    {
        if (empty($deliveryOrder->getId())) {
            ObjectPersister::save(function() use ($deliveryOrder) {
                $this->deliveryOrderRepository->add($deliveryOrder);
            });
        } else {
            ObjectPersister::save(function() use ($deliveryOrder) {
                $this->deliveryOrderRepository->update($deliveryOrder);
            });
        }
    }
    
    public function delete(DeliveryOrder $deliveryOrder)
    {
        $this->beforeDelete($deliveryOrder);
        if (!empty($deliveryOrder->getId())) {
            ObjectPersister::save(function() use ($deliveryOrder) {
                $this->deliveryOrderRepository->remove($deliveryOrder);
            });
        }
    }
    
    protected function beforeDelete(DeliveryOrder $deliveryOrder)
    {
        $this->sync($deliveryOrder);
    }
}
