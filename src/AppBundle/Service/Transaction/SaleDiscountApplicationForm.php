<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SaleDiscountApplication;
use AppBundle\Repository\Transaction\SaleDiscountApplicationRepository;

class SaleDiscountApplicationForm
{
    private $saleDiscountApplicationRepository;
    
    public function __construct(SaleDiscountApplicationRepository $saleDiscountApplicationRepository)
    {
        $this->saleDiscountApplicationRepository = $saleDiscountApplicationRepository;
    }
    
    public function initialize(SaleDiscountApplication $saleDiscountApplication, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($saleDiscountApplication->getId())) {
            $saleDiscountApplication->setApprovedPrice('0.00');
            $saleDiscountApplication->setStaffFirst($staff);
        }
        $saleDiscountApplication->setStaffLast($staff);
    }
    
    public function finalize(SaleDiscountApplication $saleDiscountApplication, array $params = array())
    {
        if (empty($saleDiscountApplication->getId())) {
            $transactionDate = $saleDiscountApplication->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastSaleDiscountApplication = $this->saleDiscountApplicationRepository->findRecentBy($year, $month);
                $currentSaleDiscountApplication = ($lastSaleDiscountApplication === null) ? $saleDiscountApplication : $lastSaleDiscountApplication;
                $saleDiscountApplication->setCodeNumberToNext($currentSaleDiscountApplication->getCodeNumber(), $year, $month);
            }
        }
        $this->sync($saleDiscountApplication);
    }
    
    private function sync(SaleDiscountApplication $saleDiscountApplication)
    {
        $grandTotal = $saleDiscountApplication->getOtherPricingAmount1() + $saleDiscountApplication->getOtherPricingAmount2() + $saleDiscountApplication->getOtherPricingAmount3() + $saleDiscountApplication->getOtherPricingAmount4() + $saleDiscountApplication->getOtherPricingAmount5();
        $totalPrice = $saleDiscountApplication->getUnitPrice() * $saleDiscountApplication->getRequestQuantity();
        $totalOtherUnitPrice = $saleDiscountApplication->getOtherUnitPrice() * $saleDiscountApplication->getRequestQuantity();
        $subTotalUnitPrice = $saleDiscountApplication->getUnitPrice() + $saleDiscountApplication->getOtherUnitPrice();
        $grandTotalUnitPrice = $totalPrice + $totalOtherUnitPrice;
        $saleDiscountApplication->setTotalPrice($totalPrice);
        $saleDiscountApplication->setGrandTotalPrice($grandTotal);
        $saleDiscountApplication->setSubTotalPrice(0);
        $saleDiscountApplication->setTotalOtherUnitPrice($totalOtherUnitPrice);
        $saleDiscountApplication->setSubTotalUnitPrice($subTotalUnitPrice);
        $saleDiscountApplication->setGrandTotalUnitPrice($grandTotalUnitPrice);
        $saleDiscountApplication->setRegistrationPrice(0);
//        $customerStatusType = $saleDiscountApplication->getCustomerStatusType();
//        if ($customerStatusType !== SaleDiscountApplication::CUSTOMER_STATUS_TYPE_OTHER) {
//            $saleDiscountApplication->setCustomerStatusName($customerStatusType);
//        }
        $paymentMethodType = $saleDiscountApplication->getPaymentMethodType();
        if ($paymentMethodType !== SaleDiscountApplication::PAYMENT_METHOD_FINANCE_COMPANY) {
            $saleDiscountApplication->setFinanceCompany(null);
        }
    }
    
    public function save(SaleDiscountApplication $saleDiscountApplication)
    {
        if (empty($saleDiscountApplication->getId())) {
            ObjectPersister::save(function() use ($saleDiscountApplication) {
                $this->saleDiscountApplicationRepository->add($saleDiscountApplication);
            });
        } else {
            ObjectPersister::save(function() use ($saleDiscountApplication) {
                $this->saleDiscountApplicationRepository->update($saleDiscountApplication);
            });
        }
    }
    
    public function delete(SaleDiscountApplication $saleDiscountApplication)
    {
        $this->beforeDelete($saleDiscountApplication);
        if (!empty($saleDiscountApplication->getId())) {
            ObjectPersister::save(function() use ($saleDiscountApplication) {
                $this->saleDiscountApplicationRepository->remove($saleDiscountApplication);
            });
        }
    }
    
    protected function beforeDelete(SaleDiscountApplication $saleDiscountApplication)
    {
        $this->sync($saleDiscountApplication);
    }
}
