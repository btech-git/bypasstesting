<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;
use AppBundle\Repository\Transaction\PurchaseDeliveryOrderRepository;
use AppBundle\Repository\Master\SupplierRepository;

class PurchaseDeliveryOrderForm
{
    private $purchaseDeliveryOrderRepository;
    private $supplierRepository;
    
    public function __construct(PurchaseDeliveryOrderRepository $purchaseDeliveryOrderRepository, SupplierRepository $supplierRepository)
    {
        $this->purchaseDeliveryOrderRepository = $purchaseDeliveryOrderRepository;
        $this->supplierRepository = $supplierRepository;
    }
    
    public function initialize(PurchaseDeliveryOrder $purchaseDeliveryOrder, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($purchaseDeliveryOrder->getId())) {
            $purchaseDeliveryOrder->setStaffFirst($staff);
            $purchaseDeliveryOrder->setSupplier($this->supplierRepository->findMainUnitRecord());
        }
        $purchaseDeliveryOrder->setStaffLast($staff);
    }
    
    public function finalize(PurchaseDeliveryOrder $purchaseDeliveryOrder, array $params = array())
    {
        if (empty($purchaseDeliveryOrder->getId())) {
            $transactionDate = $purchaseDeliveryOrder->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastPurchaseDeliveryOrderApplication = $this->purchaseDeliveryOrderRepository->findRecentBy($year, $month);
                $currentPurchaseDeliveryOrder = ($lastPurchaseDeliveryOrderApplication === null) ? $purchaseDeliveryOrder : $lastPurchaseDeliveryOrderApplication;
                $purchaseDeliveryOrder->setCodeNumberToNext($currentPurchaseDeliveryOrder->getCodeNumber(), $year, $month);
            }
        }
        $this->sync($purchaseDeliveryOrder);
    }
    
    private function sync(PurchaseDeliveryOrder $purchaseDeliveryOrder)
    {
        $purchaseDeliveryOrder->sync();
        
        $transactionDate = $purchaseDeliveryOrder->getTransactionDate();
        if ($transactionDate !== null) {
            $date = clone $transactionDate;
            $supplier = $purchaseDeliveryOrder->getSupplier();
            $creditPaymentTerm = $supplier->getCreditPaymentTerm();
            $purchaseDeliveryOrder->setDueDate($date->add(date_interval_create_from_date_string("{$creditPaymentTerm} days")));
        }
        
        $saleOrder = $purchaseDeliveryOrder->getSaleOrder();
        if ($saleOrder !== null) {
            $purchaseDeliveryOrder->setVehicleModel($saleOrder->getVehicleModel());
            $currentPurchaseDeliveryOrders = $saleOrder->getPurchaseDeliveryOrders();
            $oldPurchaseDeliveryOrders = $currentPurchaseDeliveryOrders->getValues();
            if (!in_array($purchaseDeliveryOrder, $oldPurchaseDeliveryOrders)) {
                $currentPurchaseDeliveryOrders->add($purchaseDeliveryOrder);
            }
            $purchaseDeliveryOrdersCount = $currentPurchaseDeliveryOrders->count();
            $saleOrder->setRemaining($saleOrder->getQuantity() - $purchaseDeliveryOrdersCount);
        }
    }
    
    public function save(PurchaseDeliveryOrder $purchaseDeliveryOrder)
    {
        if (empty($purchaseDeliveryOrder->getId())) {
            ObjectPersister::save(function() use ($purchaseDeliveryOrder) {
                $this->purchaseDeliveryOrderRepository->add($purchaseDeliveryOrder);
            });
        } else {
            ObjectPersister::save(function() use ($purchaseDeliveryOrder) {
                $this->purchaseDeliveryOrderRepository->update($purchaseDeliveryOrder);
            });
        }
    }
    
    public function delete(PurchaseDeliveryOrder $purchaseDeliveryOrder)
    {
        $this->beforeDelete($purchaseDeliveryOrder);
        if (!empty($purchaseDeliveryOrder->getId())) {
            ObjectPersister::save(function() use ($purchaseDeliveryOrder) {
                $this->purchaseDeliveryOrderRepository->remove($purchaseDeliveryOrder);
            });
        }
    }
    
    protected function beforeDelete(PurchaseDeliveryOrder $purchaseDeliveryOrder)
    {
        $purchaseDeliveryOrder->sync();
        
        $saleOrder = $purchaseDeliveryOrder->getSaleOrder();
        if ($saleOrder !== null) {
            $purchaseDeliveryOrder->setVehicleModel($saleOrder->getVehicleModel());
            $currentPurchaseDeliveryOrders = $saleOrder->getPurchaseDeliveryOrders();
            $oldPurchaseDeliveryOrders = $currentPurchaseDeliveryOrders->getValues();
            if (in_array($purchaseDeliveryOrder, $oldPurchaseDeliveryOrders)) {
                $currentPurchaseDeliveryOrders->removeElement($purchaseDeliveryOrder);
            }
            $purchaseDeliveryOrdersCount = $currentPurchaseDeliveryOrders->count();
            $saleOrder->setRemaining($saleOrder->getQuantity() - $purchaseDeliveryOrdersCount);
        }
    }
}
