<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\Employee;
use AppBundle\Entity\Master\Customer;
use AppBundle\Entity\Master\VehicleModel;
use AppBundle\Entity\Master\FinanceCompany;

/**
 * @ORM\Table(name="transaction_sale_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\SaleOrderRepository")
 * @Assert\Expression("(this.getIsCash() and !this.getIsLeasing()) or (!this.getIsCash() and this.getIsLeasing() and this.getLeasingMonthlyNominal() > 0)")
 */
class SaleOrder extends CodeNumberEntity
{
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
    private $quotationNumber;
    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     */
    private $purchaseOrderDate;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $purchaseOrderNumber;
    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     */
    private $deliveryDate;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $invoiceRegistrationName;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isOffTheRoad;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $vehicleBrand;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $vehicleSerialNumber;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $vehicleColor;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isWorkshopNeeded;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     */
    private $vehicleOptionalInfo;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     */
    private $vehicleAccessoriesInfo;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     */
    private $vehicleOtherInfo;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $quantity;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $remaining;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $unitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $total;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isCash;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isLeasing;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $leasingTerm;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $leasingMonthlyNominal;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $downPayment;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $downPaymentRemaining;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     */
    private $deliveryAddress;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $roleApproval;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isStock = false;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isApproved = false;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isRejected = false;
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     */
    private $staffApprovalHead;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     */
    private $staffApprovalManager;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     */
    private $staffApprovalDirector;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     */
    private $staffReject;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\FinanceCompany")
     */
    private $financeCompany;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Customer")
     * @Assert\NotNull()
     */
    private $customer;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\VehicleModel", inversedBy="saleOrders")
     * @Assert\NotNull()
     */
    private $vehicleModel;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseDeliveryOrder", mappedBy="saleOrder")
     */
    private $purchaseDeliveryOrders;
    /**
     * @ORM\OneToMany(targetEntity="SaleInvoiceDownpayment", mappedBy="saleOrder")
     */
    private $saleInvoiceDownpayments;
    /**
     * @ORM\OneToOne(targetEntity="PurchaseWorkshopHeader", mappedBy="saleOrder")
     */
    private $purchaseWorkshopHeader;
    
    public function __construct()
    {
        $this->purchaseDeliveryOrders = new ArrayCollection();
        $this->saleInvoiceDownpayments = new ArrayCollection();
    }
    
    public function getCodeNumberConstant()
    {
        return 'SPK';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }
    
    public function getQuotationNumber() { return $this->quotationNumber; }
    public function setQuotationNumber($quotationNumber) { $this->quotationNumber = $quotationNumber; }

    public function getPurchaseOrderDate() { return $this->purchaseOrderDate; }
    public function setPurchaseOrderDate($purchaseOrderDate) { $this->purchaseOrderDate = $purchaseOrderDate; }

    public function getPurchaseOrderNumber() { return $this->purchaseOrderNumber; }
    public function setPurchaseOrderNumber($purchaseOrderNumber) { $this->purchaseOrderNumber = $purchaseOrderNumber; }

    public function getDeliveryDate() { return $this->deliveryDate; }
    public function setDeliveryDate($deliveryDate) { $this->deliveryDate = $deliveryDate; }

    public function getInvoiceRegistrationName() { return $this->invoiceRegistrationName; }
    public function setInvoiceRegistrationName($invoiceRegistrationName) { $this->invoiceRegistrationName = $invoiceRegistrationName; }

    public function getIsOffTheRoad() { return $this->isOffTheRoad; }
    public function setIsOffTheRoad($isOffTheRoad) { $this->isOffTheRoad = $isOffTheRoad; }

    public function getVehicleBrand() { return $this->vehicleBrand; }
    public function setVehicleBrand($vehicleBrand) { $this->vehicleBrand = $vehicleBrand; }

    public function getVehicleSerialNumber() { return $this->vehicleSerialNumber; }
    public function setVehicleSerialNumber($vehicleSerialNumber) { $this->vehicleSerialNumber = $vehicleSerialNumber; }

    public function getVehicleColor() { return $this->vehicleColor; }
    public function setVehicleColor($vehicleColor) { $this->vehicleColor = $vehicleColor; }

    public function getIsWorkshopNeeded() { return $this->isWorkshopNeeded; }
    public function setIsWorkshopNeeded($isWorkshopNeeded) { $this->isWorkshopNeeded = $isWorkshopNeeded; }

    public function getVehicleOptionalInfo() { return $this->vehicleOptionalInfo; }
    public function setVehicleOptionalInfo($vehicleOptionalInfo) { $this->vehicleOptionalInfo = $vehicleOptionalInfo; }

    public function getVehicleAccessoriesInfo() { return $this->vehicleAccessoriesInfo; }
    public function setVehicleAccessoriesInfo($vehicleAccessoriesInfo) { $this->vehicleAccessoriesInfo = $vehicleAccessoriesInfo; }

    public function getVehicleOtherInfo() { return $this->vehicleOtherInfo; }
    public function setVehicleOtherInfo($vehicleOtherInfo) { $this->vehicleOtherInfo = $vehicleOtherInfo; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getRemaining() { return $this->remaining; }
    public function setRemaining($remaining) { $this->remaining = $remaining; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getIsCash() { return $this->isCash; }
    public function setIsCash($isCash) { $this->isCash = $isCash; }

    public function getIsLeasing() { return $this->isLeasing; }
    public function setIsLeasing($isLeasing) { $this->isLeasing = $isLeasing; }

    public function getLeasingTerm() { return $this->leasingTerm; }
    public function setLeasingTerm($leasingTerm) { $this->leasingTerm = $leasingTerm; }

    public function getLeasingMonthlyNominal() { return $this->leasingMonthlyNominal; }
    public function setLeasingMonthlyNominal($leasingMonthlyNominal) { $this->leasingMonthlyNominal = $leasingMonthlyNominal; }

    public function getDownPayment() { return $this->downPayment; }
    public function setDownPayment($downPayment) { $this->downPayment = $downPayment; }

    public function getDownPaymentRemaining() { return $this->downPaymentRemaining; }
    public function setDownPaymentRemaining($downPaymentRemaining) { $this->downPaymentRemaining = $downPaymentRemaining; }

    public function getDeliveryAddress() { return $this->deliveryAddress; }
    public function setDeliveryAddress($deliveryAddress) { $this->deliveryAddress = $deliveryAddress; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getRoleApproval() { return $this->roleApproval; }
    public function setRoleApproval($roleApproval) { $this->roleApproval = $roleApproval; }

    public function getIsStock() { return $this->isStock; }
    public function setIsStock($isStock) { $this->isStock = $isStock; }

    public function getIsApproved() { return $this->isApproved; }
    public function setIsApproved($isApproved) { $this->isApproved = $isApproved; }

    public function getIsRejected() { return $this->isRejected; }
    public function setIsRejected($isRejected) { $this->isRejected = $isRejected; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getStaffApprovalHead() { return $this->staffApprovalHead; }
    public function setStaffApprovalHead(Staff $staffApprovalHead = null) { $this->staffApprovalHead = $staffApprovalHead; }

    public function getStaffApprovalManager() { return $this->staffApprovalManager; }
    public function setStaffApprovalManager(Staff $staffApprovalManager = null) { $this->staffApprovalManager = $staffApprovalManager; }

    public function getStaffApprovalDirector() { return $this->staffApprovalDirector; }
    public function setStaffApprovalDirector(Staff $staffApprovalDirector = null) { $this->staffApprovalDirector = $staffApprovalDirector; }

    public function getStaffReject() { return $this->staffReject; }
    public function setStaffReject(Staff $staffReject = null) { $this->staffReject = $staffReject; }

    public function getFinanceCompany() { return $this->financeCompany; }
    public function setFinanceCompany(FinanceCompany $financeCompany = null) { $this->financeCompany = $financeCompany; }

    public function getCustomer() { return $this->customer; }
    public function setCustomer(Customer $customer = null) { $this->customer = $customer; }

    public function getVehicleModel() { return $this->vehicleModel; }
    public function setVehicleModel(VehicleModel $vehicleModel = null) { $this->vehicleModel = $vehicleModel; }

    public function getPurchaseDeliveryOrders() { return $this->purchaseDeliveryOrders; }
    public function setPurchaseDeliveryOrders(Collection $purchaseDeliveryOrders) { $this->purchaseDeliveryOrders = $purchaseDeliveryOrders; }

    public function getSaleInvoiceDownpayments() { return $this->saleInvoiceDownpayments; }
    public function setSaleInvoiceDownpayments(Collection $saleInvoiceDownpayments) { $this->saleInvoiceDownpayments = $saleInvoiceDownpayments; }
    
    public function getPurchaseWorkshopHeader() { return $this->purchaseWorkshopHeader; }
    public function setPurchaseWorkshopHeader(PurchaseWorkshopHeader $purchaseWorkshopHeader = null) { $this->purchaseWorkshopHeader = $purchaseWorkshopHeader; }
}
