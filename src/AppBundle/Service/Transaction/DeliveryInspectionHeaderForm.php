<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\DeliveryInspectionHeader;
use AppBundle\Repository\Transaction\DeliveryInspectionHeaderRepository;

class DeliveryInspectionHeaderForm
{
    private $deliveryInspectionHeaderRepository;
    
    public function __construct(DeliveryInspectionHeaderRepository $deliveryInspectionHeaderRepository)
    {
        $this->deliveryInspectionHeaderRepository = $deliveryInspectionHeaderRepository;
    }
    
    public function initialize(DeliveryInspectionHeader $deliveryInspectionHeader, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($deliveryInspectionHeader->getId())) {
            $deliveryInspectionHeader->setStaffFirst($staff);
        }
        $deliveryInspectionHeader->setStaffLast($staff);
    }
    
    public function finalize(DeliveryInspectionHeader $deliveryInspectionHeader, array $params = array())
    {
        if (empty($deliveryInspectionHeader->getId())) {
            $transactionDate = $deliveryInspectionHeader->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastDeliveryInspectionHeaderApplication = $this->deliveryInspectionHeaderRepository->findRecentBy($year, $month);
                $currentDeliveryInspectionHeader = ($lastDeliveryInspectionHeaderApplication === null) ? $deliveryInspectionHeader : $lastDeliveryInspectionHeaderApplication;
                $deliveryInspectionHeader->setCodeNumberToNext($currentDeliveryInspectionHeader->getCodeNumber(), $year, $month);
            }
        }
        foreach ($deliveryInspectionHeader->getDeliveryInspectionDetails() as $deliveryInspectionDetails) {
            $deliveryInspectionDetails->setDeliveryInspectionHeader($deliveryInspectionHeader);
        }
        $this->sync($deliveryInspectionHeader);
    }
    
    private function sync(DeliveryInspectionHeader $deliveryInspectionHeader)
    {
//        $total = $deliveryInspectionHeader->getQuantity() * $deliveryInspectionHeader->getUnitPrice();
//        $deliveryInspectionHeader->setTotal($total);
    }
    
    public function save(DeliveryInspectionHeader $deliveryInspectionHeader)
    {
        if (empty($deliveryInspectionHeader->getId())) {
            ObjectPersister::save(function() use ($deliveryInspectionHeader) {
                $this->deliveryInspectionHeaderRepository->add($deliveryInspectionHeader, array(
                    'deliveryInspectionDetails' => array('add' => true),
                ));
            });
        } else {
            ObjectPersister::save(function() use ($deliveryInspectionHeader) {
                $this->deliveryInspectionHeaderRepository->update($deliveryInspectionHeader, array(
                    'deliveryInspectionDetails' => array('add' => true, 'remove' => true),
                ));
            });
        }
    }
    
    public function delete(DeliveryInspectionHeader $deliveryInspectionHeader)
    {
        $this->beforeDelete($deliveryInspectionHeader);
        if (!empty($deliveryInspectionHeader->getId())) {
            ObjectPersister::save(function() use ($deliveryInspectionHeader) {
                $this->deliveryInspectionHeaderRepository->remove($deliveryInspectionHeader, array(
                    'deliveryInspectionDetails' => array('remove' => true),
                ));
            });
        }
    }
    
    protected function beforeDelete(DeliveryInspectionHeader $deliveryInspectionHeader)
    {
        $deliveryInspectionHeader->getDeliveryInspectionDetails()->clear();
        $this->sync($deliveryInspectionHeader);
    }
}
