<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SaleOrder;
use AppBundle\Repository\Transaction\SaleOrderRepository;

class SaleOrderForm
{
    private $saleOrderRepository;
    
    public function __construct(SaleOrderRepository $saleOrderRepository)
    {
        $this->saleOrderRepository = $saleOrderRepository;
    }
    
    public function initialize(SaleOrder $saleOrder, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($saleOrder->getId())) {
            $saleOrder->setStaffFirst($staff);
            $saleOrder->setVehicleBrand('HINO');
            $saleOrder->setRoleApproval('');
        }
        $saleOrder->setStaffLast($staff);
    }
    
    public function finalize(SaleOrder $saleOrder, array $params = array())
    {
        if (empty($saleOrder->getId())) {
            $transactionDate = $saleOrder->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastSaleOrderApplication = $this->saleOrderRepository->findRecentBy($year, $month);
                $currentSaleOrder = ($lastSaleOrderApplication === null) ? $saleOrder : $lastSaleOrderApplication;
                $saleOrder->setCodeNumberToNext($currentSaleOrder->getCodeNumber(), $year, $month);
            }
        }
        $this->sync($saleOrder);
    }
    
    private function sync(SaleOrder $saleOrder)
    {
        $total = $saleOrder->getQuantity() * $saleOrder->getUnitPrice();
        $saleOrder->setTotal($total);
        $saleOrder->setStaffApprovalHead(null);
        if ($saleOrder->getIsCash() && !$saleOrder->getIsLeasing()) {
            $saleOrder->setFinanceCompany(null);
            $saleOrder->setLeasingTerm('');
            $saleOrder->setLeasingMonthlyNominal('0.00');
        }
        $purchaseDeliveryOrdersCount = $saleOrder->getPurchaseDeliveryOrders()->count();
        $saleOrder->setRemaining($saleOrder->getQuantity() - $purchaseDeliveryOrdersCount);
        $saleOrder->setDownpaymentRemaining('0.00');
        $purchaseWorkshopHeader = $saleOrder->getPurchaseWorkshopHeader();
        if ($purchaseWorkshopHeader !== null) {
            $purchaseWorkshopHeader->setQuantityOrder($saleOrder->getQuantity());
        }
    }
    
    public function save(SaleOrder $saleOrder)
    {
        if (empty($saleOrder->getId())) {
            ObjectPersister::save(function() use ($saleOrder) {
                $this->saleOrderRepository->add($saleOrder);
            });
        } else {
            ObjectPersister::save(function() use ($saleOrder) {
                $this->saleOrderRepository->update($saleOrder);
            });
        }
    }
    
    public function delete(SaleOrder $saleOrder)
    {
        $this->beforeDelete($saleOrder);
        if (!empty($saleOrder->getId())) {
            ObjectPersister::save(function() use ($saleOrder) {
                $this->saleOrderRepository->remove($saleOrder);
            });
        }
    }
    
    protected function beforeDelete(SaleOrder $saleOrder)
    {
        $this->sync($saleOrder);
    }
    
    public function approve(SaleOrder $saleOrder, $staff)
    {
        $roles = $staff->getRoles();
        $role = $roles[2];
        switch ($role) {
            case 'ROLE_DIRECTOR':
                $roleApproval = 'ROLE_BRANCH_MANAGER';
                $total = 1000000000;
                break;
            case 'ROLE_BRANCH_MANAGER':
                $roleApproval = 'ROLE_SALES_MANAGER';
                $total = 500000000;
                break;
            case 'ROLE_SALES_MANAGER':
                $roleApproval = '';
                $total = 0;
                break;
            default:
                $roleApproval = null;
                $total = null;
        }
        
        $saleOrderTotal = $saleOrder->getTotal();
        if (!$saleOrder->getIsRejected() && !$saleOrder->getIsApproved() && $saleOrder->getRoleApproval() === $roleApproval && $saleOrderTotal > $total) {
            $saleOrder->setRoleApproval($role);
            
            if ($role === 'ROLE_SALES_MANAGER') {
                $saleOrder->setStaffApprovalHead($staff);
            } else if ($role === 'ROLE_BRANCH_MANAGER') {
                $saleOrder->setStaffApprovalManager($staff);
            } else if ($role === 'ROLE_DIRECTOR') {
                $saleOrder->setStaffApprovalDirector($staff);
            }
            
            $isApprovedHead = ($role === 'ROLE_SALES_MANAGER' && $saleOrderTotal > 0 && $saleOrderTotal <= 500000000);
            $isApprovedManager = ($role === 'ROLE_BRANCH_MANAGER' && $saleOrderTotal > 500000000 && $saleOrderTotal <= 1000000000);
            $isApprovedDirector = ($role === 'ROLE_DIRECTOR' && $saleOrderTotal > 1000000000);
            if ($isApprovedHead || $isApprovedManager || $isApprovedDirector) {
                $saleOrder->setIsApproved(true);
            }
            
            ObjectPersister::save(function() use ($saleOrder) {
                $this->saleOrderRepository->update($saleOrder);
            });
        }
    }
    
    public function reject(SaleOrder $saleOrder, $staff)
    {
        if (!$saleOrder->getIsRejected() && !$saleOrder->getIsApproved()) {
            $saleOrder->setStaffReject($staff);
            
            $saleOrder->setIsRejected(true);
            
            ObjectPersister::save(function() use ($saleOrder) {
                $this->saleOrderRepository->update($saleOrder);
            });
        }
    }
}
