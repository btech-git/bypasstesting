<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\Customer;
use AppBundle\Entity\Master\FinanceCompany;
use AppBundle\Entity\Master\VehicleModel;
use AppBundle\Entity\Master\Supplier;

/**
 * @ORM\Table(name="transaction_sale_discount_application")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\SaleDiscountApplicationRepository")
 */
class SaleDiscountApplication extends CodeNumberEntity
{
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_FINANCE_COMPANY = 'leasing';
    const TRANSACTION_STATUS_NEW = 'new';
    const TRANSACTION_STATUS_REPEAT = 'repeat';
    const LEASING_STATUS_MATCHED = 'ya';
    const LEASING_STATUS_UNMATCHED = 'tidak';
    const OWNERSHIP_STATUS_OFFTHEROAD = 'Off TR';
    const OWNERSHIP_STATUS_ONTHEROAD = 'On TR';
    const OWNERSHIP_CATEGORY_BLACK = 'Hitam';
    const OWNERSHIP_CATEGORY_YELLOW = 'Kuning';
    const WORKSHOP_REFERENCE_PARTNER = 'Rekanan';
    const WORKSHOP_REFERENCE_SALESMAN = 'Salesman';
    const WORKSHOP_REFERENCE_CUSTOMER = 'Customer';
    const LEASING_REFERENCE_PARTNER = 'Rekanan';
    const LEASING_REFERENCE_OTHER = 'Referensi';
    const LEASING_REFERENCE_CUSTOMER = 'Customer';
    
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull() @Assert\Date()
     */
    private $transactionDate;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $assemblyYear;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $vehicleColor;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $orderArea;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $workshopType;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $workshopReference;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $workshopPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $workshopProfit;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isWorkshopSplitPurchase;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $deliveryType;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $ownershipStatus;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $ownershipCategory;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $transactionStatus;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $requestQuantity;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $paymentMethodType;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isCashBeforeDelivery;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isPaymentTerm;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $termOfPayment;
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $leasingReference;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $unitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $totalPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $otherUnitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $totalOtherUnitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $subTotalUnitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $grandTotalUnitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $approvedPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $bookingFee;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $downpayment1;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $downpayment2;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalPayment;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $leasingPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $leasingPriceDifference;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $leasingOverPaid;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $leasingOverPaidNett;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $leasingTaxAmount;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $leasingStatus;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $registrationPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $subTotalPrice;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $otherPricingName1;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $otherPricingName2;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $otherPricingName3;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $otherPricingName4;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $otherPricingName5;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $otherPricingAmount1;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $otherPricingAmount2;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $otherPricingAmount3;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $otherPricingAmount4;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $otherPricingAmount5;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $grandTotalPrice;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $mediatorName;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $mediatorRanking;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $mediatorPhone;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $mediatorTaxSelection;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $mediatorPrice;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     * @Assert\NotNull()
     */
    private $staffFirst;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     * @Assert\NotNull()
     */
    private $staffLast;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Customer")
     * @Assert\NotNull()
     */
    private $customer;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\FinanceCompany")
     */
    private $financeCompany;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\VehicleModel", inversedBy="saleDiscountApplications")
     */
    private $vehicleModel;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Supplier")
     */
    private $supplier;
    
    public function __construct()
    {
    }
    
    public function getCodeNumberConstant()
    {
        return 'SDC';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getAssemblyYear() { return $this->assemblyYear; }
    public function setAssemblyYear($assemblyYear) { $this->assemblyYear = $assemblyYear; }

    public function getVehicleColor() { return $this->vehicleColor; }
    public function setVehicleColor($vehicleColor) { $this->vehicleColor = $vehicleColor; }

    public function getOrderArea() { return $this->orderArea; }
    public function setOrderArea($orderArea) { $this->orderArea = $orderArea; }

    public function getWorkshopType() { return $this->workshopType; }
    public function setWorkshopType($workshopType) { $this->workshopType = $workshopType; }

    public function getWorkshopReference() { return $this->workshopReference; }
    public function setWorkshopReference($workshopReference) { $this->workshopReference = $workshopReference; }

    public function getWorkshopPrice() { return $this->workshopPrice; }
    public function setWorkshopPrice($workshopPrice) { $this->workshopPrice = $workshopPrice; }

    public function getWorkshopProfit() { return $this->workshopProfit; }
    public function setWorkshopProfit($workshopProfit) { $this->workshopProfit = $workshopProfit; }

    public function getIsWorkshopSplitPurchase() { return $this->isWorkshopSplitPurchase; }
    public function setIsWorkshopSplitPurchase($isWorkshopSplitPurchase) { $this->isWorkshopSplitPurchase = $isWorkshopSplitPurchase; }

    public function getDeliveryType() { return $this->deliveryType; }
    public function setDeliveryType($deliveryType) { $this->deliveryType = $deliveryType; }

    public function getOwnershipStatus() { return $this->ownershipStatus; }
    public function setOwnershipStatus($ownershipStatus) { $this->ownershipStatus = $ownershipStatus; }

    public function getOwnershipCategory() { return $this->ownershipCategory; }
    public function setOwnershipCategory($ownershipCategory) { $this->ownershipCategory = $ownershipCategory; }

    public function getTransactionStatus() { return $this->transactionStatus; }
    public function setTransactionStatus($transactionStatus) { $this->transactionStatus = $transactionStatus; }

    public function getRequestQuantity() { return $this->requestQuantity; }
    public function setRequestQuantity($requestQuantity) { $this->requestQuantity = $requestQuantity; }

    public function getPaymentMethodType() { return $this->paymentMethodType; }
    public function setPaymentMethodType($paymentMethodType) { $this->paymentMethodType = $paymentMethodType; }

    public function getIsCashBeforeDelivery() { return $this->isCashBeforeDelivery; }
    public function setIsCashBeforeDelivery($isCashBeforeDelivery) { $this->isCashBeforeDelivery = $isCashBeforeDelivery; }

    public function getIsPaymentTerm() { return $this->isPaymentTerm; }
    public function setIsPaymentTerm($isPaymentTerm) { $this->isPaymentTerm = $isPaymentTerm; }

    public function getTermOfPayment() { return $this->termOfPayment; }
    public function setTermOfPayment($termOfPayment) { $this->termOfPayment = $termOfPayment; }

    public function getLeasingReference() { return $this->leasingReference; }
    public function setLeasingReference($leasingReference) { $this->leasingReference = $leasingReference; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getTotalPrice() { return $this->totalPrice; }
    public function setTotalPrice($totalPrice) { $this->totalPrice = $totalPrice; }

    public function getOtherUnitPrice() { return $this->otherUnitPrice; }
    public function setOtherUnitPrice($otherUnitPrice) { $this->otherUnitPrice = $otherUnitPrice; }

    public function getTotalOtherUnitPrice() { return $this->totalOtherUnitPrice; }
    public function setTotalOtherUnitPrice($totalOtherUnitPrice) { $this->totalOtherUnitPrice = $totalOtherUnitPrice; }

    public function getSubTotalUnitPrice() { return $this->subTotalUnitPrice; }
    public function setSubTotalUnitPrice($subTotalUnitPrice) { $this->subTotalUnitPrice = $subTotalUnitPrice; }

    public function getGrandTotalUnitPrice() { return $this->grandTotalUnitPrice; }
    public function setGrandTotalUnitPrice($grandTotalUnitPrice) { $this->grandTotalUnitPrice = $grandTotalUnitPrice; }

    public function getApprovedPrice() { return $this->approvedPrice; }
    public function setApprovedPrice($approvedPrice) { $this->approvedPrice = $approvedPrice; }
    
    public function getBookingFee() { return $this->bookingFee; }
    public function setBookingFee($bookingFee) { $this->bookingFee = $bookingFee; }

    public function getDownpayment1() { return $this->downpayment1; }
    public function setDownpayment1($downpayment1) { $this->downpayment1 = $downpayment1; }

    public function getDownpayment2() { return $this->downpayment2; }
    public function setDownpayment2($downpayment2) { $this->downpayment2 = $downpayment2; }

    public function getTotalPayment() { return $this->totalPayment; }
    public function setTotalPayment($totalPayment) { $this->totalPayment = $totalPayment; }

    public function getLeasingPrice() { return $this->leasingPrice; }
    public function setLeasingPrice($leasingPrice) { $this->leasingPrice = $leasingPrice; }

    public function getLeasingPriceDifference() { return $this->leasingPriceDifference; }
    public function setLeasingPriceDifference($leasingPriceDifference) { $this->leasingPriceDifference = $leasingPriceDifference; }

    public function getLeasingOverPaid() { return $this->leasingOverPaid; }
    public function setLeasingOverPaid($leasingOverPaid) { $this->leasingOverPaid = $leasingOverPaid; }

    public function getLeasingOverPaidNett() { return $this->leasingOverPaidNett; }
    public function setLeasingOverPaidNett($leasingOverPaidNett) { $this->leasingOverPaidNett = $leasingOverPaidNett; }

    public function getLeasingTaxAmount() { return $this->leasingTaxAmount; }
    public function setLeasingTaxAmount($leasingTaxAmount) { $this->leasingTaxAmount = $leasingTaxAmount; }

    public function getLeasingStatus() { return $this->leasingStatus; }
    public function setLeasingStatus($leasingStatus) { $this->leasingStatus = $leasingStatus; }

    public function getRegistrationPrice() { return $this->registrationPrice; }
    public function setRegistrationPrice($registrationPrice) { $this->registrationPrice = $registrationPrice; }

    public function getSubTotalPrice() { return $this->subTotalPrice; }
    public function setSubTotalPrice($subTotalPrice) { $this->subTotalPrice = $subTotalPrice; }

    public function getOtherPricingName1() { return $this->otherPricingName1; }
    public function setOtherPricingName1($otherPricingName1) { $this->otherPricingName1 = $otherPricingName1; }

    public function getOtherPricingName2() { return $this->otherPricingName2; }
    public function setOtherPricingName2($otherPricingName2) { $this->otherPricingName2 = $otherPricingName2; }

    public function getOtherPricingName3() { return $this->otherPricingName3; }
    public function setOtherPricingName3($otherPricingName3) { $this->otherPricingName3 = $otherPricingName3; }

    public function getOtherPricingName4() { return $this->otherPricingName4; }
    public function setOtherPricingName4($otherPricingName4) { $this->otherPricingName4 = $otherPricingName4; }

    public function getOtherPricingName5() { return $this->otherPricingName5; }
    public function setOtherPricingName5($otherPricingName5) { $this->otherPricingName5 = $otherPricingName5; }

    public function getOtherPricingAmount1() { return $this->otherPricingAmount1; }
    public function setOtherPricingAmount1($otherPricingAmount1) { $this->otherPricingAmount1 = $otherPricingAmount1; }

    public function getOtherPricingAmount2() { return $this->otherPricingAmount2; }
    public function setOtherPricingAmount2($otherPricingAmount2) { $this->otherPricingAmount2 = $otherPricingAmount2; }

    public function getOtherPricingAmount3() { return $this->otherPricingAmount3; }
    public function setOtherPricingAmount3($otherPricingAmount3) { $this->otherPricingAmount3 = $otherPricingAmount3; }

    public function getOtherPricingAmount4() { return $this->otherPricingAmount4; }
    public function setOtherPricingAmount4($otherPricingAmount4) { $this->otherPricingAmount4 = $otherPricingAmount4; }

    public function getOtherPricingAmount5() { return $this->otherPricingAmount5; }
    public function setOtherPricingAmount5($otherPricingAmount5) { $this->otherPricingAmount5 = $otherPricingAmount5; }

    public function getGrandTotalPrice() { return $this->grandTotalPrice; }
    public function setGrandTotalPrice($grandTotalPrice) { $this->grandTotalPrice = $grandTotalPrice; }

    public function getMediatorName() { return $this->mediatorName; }
    public function setMediatorName($mediatorName) { $this->mediatorName = $mediatorName; }

    public function getMediatorRanking() { return $this->mediatorRanking; }
    public function setMediatorRanking($mediatorRanking) { $this->mediatorRanking = $mediatorRanking; }

    public function getMediatorPhone() { return $this->mediatorPhone; }
    public function setMediatorPhone($mediatorPhone) { $this->mediatorPhone = $mediatorPhone; }

    public function getMediatorTaxSelection() { return $this->mediatorTaxSelection; }
    public function setMediatorTaxSelection($mediatorTaxSelection) { $this->mediatorTaxSelection = $mediatorTaxSelection; }

    public function getMediatorPrice() { return $this->mediatorPrice; }
    public function setMediatorPrice($mediatorPrice) { $this->mediatorPrice = $mediatorPrice; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getCustomer() { return $this->customer; }
    public function setCustomer(Customer $customer = null) { $this->customer = $customer; }

    public function getFinanceCompany() { return $this->financeCompany; }
    public function setFinanceCompany(FinanceCompany $financeCompany = null) { $this->financeCompany = $financeCompany; }
    
    public function getVehicleModel() { return $this->vehicleModel; }
    public function setVehicleModel(VehicleModel $vehicleModel = null) { $this->vehicleModel = $vehicleModel; }
    
    public function getSupplier() { return $this->supplier; }
    public function setSupplier(Supplier $supplier = null) { $this->supplier = $supplier; }
}
