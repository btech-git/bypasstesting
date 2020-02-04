<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\SaleOrder;
use AppBundle\Repository\Transaction\SaleOrderRepository;

class SaleOrderStockForm
{
    private $saleOrderRepository;
    
    public function __construct(SaleOrderRepository $saleOrderRepository)
    {
        $this->saleOrderRepository = $saleOrderRepository;
    }
    
    public function initialize(SaleOrder $saleOrder, array $params = array())
    {
        $saleOrder->setIsStock(true);
    }
    
    public function finalize(SaleOrder $saleOrder, array $params = array())
    {
        $purchaseDeliveryOrders = $saleOrder->getPurchaseDeliveryOrders();
        foreach ($purchaseDeliveryOrders as $purchaseDeliveryOrder) {
            $purchaseDeliveryOrder->setSaleOrder($saleOrder);
        }
        $saleOrder->setRemaining($saleOrder->getQuantity() - $purchaseDeliveryOrders->count());
    }
    
    public function save(SaleOrder $saleOrder)
    {
        $this->saleOrderRepository->update($saleOrder, array(
            'purchaseDeliveryOrders' => array('add' => true),
        ));
    }
    
    public function isValidForStockReferring(SaleOrder $saleOrder)
    {
        if ($saleOrder->getRemaining() === 0) {
            return false;
        } else {
            foreach ($saleOrder->getPurchaseDeliveryOrders() as $purchaseDeliveryOrder) {
                if (!$purchaseDeliveryOrder->getIsStock()) {
                    return false;
                }
            }
            return true;
        }
    }
}
