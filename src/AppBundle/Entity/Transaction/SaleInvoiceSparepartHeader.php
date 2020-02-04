<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="transaction_sale_invoice_sparepart_header")
 * @ORM\Entity
 */
class SaleInvoiceSparepartHeader
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $invoiceNumber;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $workOrderNumber;
    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull() @Assert\Date()
     */
    private $transactionDate;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $companyName;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $taxNumber;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $prmCpc;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $prmTrx;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $customerCode;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $customerName;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $vehicleModel;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $vehicleChassisNumber;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $vehicleEngineNumber;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $vehicleLicenseNumber;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $vehicleClaimNumber;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $vehiclePolicyNumber;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalPackage;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalOpr;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalPart;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalOil;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalMaterial;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalAccessory;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalSublet;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalSouvenir;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountPackage;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountOpr;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountPart;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountOil;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountMaterial;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountAccessory;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountSublet;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountSouvenir;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $grandTotalBeforeTax;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $grandTotalTax;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $grandTotalAfterTax;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalSaleAmount;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $paymentType;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $cpcDescription;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $trxDescription;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $taxInvoiceDocumentNumber;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(1990)
     */
    private $productionYear;
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $serviceMileage;
    /**
     * @ORM\OneToMany(targetEntity="SaleInvoiceSparepartDetail", mappedBy="saleInvoiceSparepartHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $saleInvoiceSparepartDetails;
    
    public function __construct()
    {
        $this->saleInvoiceSparepartDetails = new ArrayCollection();
    }

    public function getId() { return $this->id; }

    public function getInvoiceNumber() { return $this->invoiceNumber; }
    public function setInvoiceNumber($invoiceNumber) { $this->invoiceNumber = $invoiceNumber; }

    public function getWorkOrderNumber() { return $this->workOrderNumber; }
    public function setWorkOrderNumber($workOrderNumber) { $this->workOrderNumber = $workOrderNumber; }

    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getCompanyName() { return $this->companyName; }
    public function setCompanyName($companyName) { $this->companyName = $companyName; }

    public function getTaxNumber() { return $this->taxNumber; }
    public function setTaxNumber($taxNumber) { $this->taxNumber = $taxNumber; }

    public function getPrmCpc() { return $this->prmCpc; }
    public function setPrmCpc($prmCpc) { $this->prmCpc = $prmCpc; }

    public function getPrmTrx() { return $this->prmTrx; }
    public function setPrmTrx($prmTrx) { $this->prmTrx = $prmTrx; }

    public function getCustomerCode() { return $this->customerCode; }
    public function setCustomerCode($customerCode) { $this->customerCode = $customerCode; }

    public function getCustomerName() { return $this->customerName; }
    public function setCustomerName($customerName) { $this->customerName = $customerName; }

    public function getVehicleModel() { return $this->vehicleModel; }
    public function setVehicleModel($vehicleModel) { $this->vehicleModel = $vehicleModel; }

    public function getVehicleChassisNumber() { return $this->vehicleChassisNumber; }
    public function setVehicleChassisNumber($vehicleChassisNumber) { $this->vehicleChassisNumber = $vehicleChassisNumber; }

    public function getVehicleEngineNumber() { return $this->vehicleEngineNumber; }
    public function setVehicleEngineNumber($vehicleEngineNumber) { $this->vehicleEngineNumber = $vehicleEngineNumber; }

    public function getVehicleLicenseNumber() { return $this->vehicleLicenseNumber; }
    public function setVehicleLicenseNumber($vehicleLicenseNumber) { $this->vehicleLicenseNumber = $vehicleLicenseNumber; }

    public function getVehicleClaimNumber() { return $this->vehicleClaimNumber; }
    public function setVehicleClaimNumber($vehicleClaimNumber) { $this->vehicleClaimNumber = $vehicleClaimNumber; }

    public function getVehiclePolicyNumber() { return $this->vehiclePolicyNumber; }
    public function setVehiclePolicyNumber($vehiclePolicyNumber) { $this->vehiclePolicyNumber = $vehiclePolicyNumber; }

    public function getTotalPackage() { return $this->totalPackage; }
    public function setTotalPackage($totalPackage) { $this->totalPackage = $totalPackage; }

    public function getTotalOpr() { return $this->totalOpr; }
    public function setTotalOpr($totalOpr) { $this->totalOpr = $totalOpr; }

    public function getTotalPart() { return $this->totalPart; }
    public function setTotalPart($totalPart) { $this->totalPart = $totalPart; }

    public function getTotalOil() { return $this->totalOil; }
    public function setTotalOil($totalOil) { $this->totalOil = $totalOil; }

    public function getTotalMaterial() { return $this->totalMaterial; }
    public function setTotalMaterial($totalMaterial) { $this->totalMaterial = $totalMaterial; }

    public function getTotalAccessory() { return $this->totalAccessory; }
    public function setTotalAccessory($totalAccessory) { $this->totalAccessory = $totalAccessory; }

    public function getTotalSublet() { return $this->totalSublet; }
    public function setTotalSublet($totalSublet) { $this->totalSublet = $totalSublet; }

    public function getTotalSouvenir() { return $this->totalSouvenir; }
    public function setTotalSouvenir($totalSouvenir) { $this->totalSouvenir = $totalSouvenir; }

    public function getDiscountPackage() { return $this->discountPackage; }
    public function setDiscountPackage($discountPackage) { $this->discountPackage = $discountPackage; }

    public function getDiscountOpr() { return $this->discountOpr; }
    public function setDiscountOpr($discountOpr) { $this->discountOpr = $discountOpr; }

    public function getDiscountPart() { return $this->discountPart; }
    public function setDiscountPart($discountPart) { $this->discountPart = $discountPart; }

    public function getDiscountOil() { return $this->discountOil; }
    public function setDiscountOil($discountOil) { $this->discountOil = $discountOil; }

    public function getDiscountMaterial() { return $this->discountMaterial; }
    public function setDiscountMaterial($discountMaterial) { $this->discountMaterial = $discountMaterial; }

    public function getDiscountAccessory() { return $this->discountAccessory; }
    public function setDiscountAccessory($discountAccessory) { $this->discountAccessory = $discountAccessory; }

    public function getDiscountSublet() { return $this->discountSublet; }
    public function setDiscountSublet($discountSublet) { $this->discountSublet = $discountSublet; }

    public function getDiscountSouvenir() { return $this->discountSouvenir; }
    public function setDiscountSouvenir($discountSouvenir) { $this->discountSouvenir = $discountSouvenir; }

    public function getGrandTotalBeforeTax() { return $this->grandTotalBeforeTax; }
    public function setGrandTotalBeforeTax($grandTotalBeforeTax) { $this->grandTotalBeforeTax = $grandTotalBeforeTax; }

    public function getGrandTotalTax() { return $this->grandTotalTax; }
    public function setGrandTotalTax($grandTotalTax) { $this->grandTotalTax = $grandTotalTax; }

    public function getGrandTotalAfterTax() { return $this->grandTotalAfterTax; }
    public function setGrandTotalAfterTax($grandTotalAfterTax) { $this->grandTotalAfterTax = $grandTotalAfterTax; }

    public function getTotalSaleAmount() { return $this->totalSaleAmount; }
    public function setTotalSaleAmount($totalSaleAmount) { $this->totalSaleAmount = $totalSaleAmount; }

    public function getPaymentType() { return $this->paymentType; }
    public function setPaymentType($paymentType) { $this->paymentType = $paymentType; }

    public function getCpcDescription() { return $this->cpcDescription; }
    public function setCpcDescription($cpcDescription) { $this->cpcDescription = $cpcDescription; }

    public function getTrxDescription() { return $this->trxDescription; }
    public function setTrxDescription($trxDescription) { $this->trxDescription = $trxDescription; }

    public function getTaxInvoiceDocumentNumber() { return $this->taxInvoiceDocumentNumber; }
    public function setTaxInvoiceDocumentNumber($taxInvoiceDocumentNumber) { $this->taxInvoiceDocumentNumber = $taxInvoiceDocumentNumber; }

    public function getProductionYear() { return $this->productionYear; }
    public function setProductionYear($productionYear) { $this->productionYear = $productionYear; }

    public function getServiceMileage() { return $this->serviceMileage; }
    public function setServiceMileage($serviceMileage) { $this->serviceMileage = $serviceMileage; }

    public function getSaleInvoiceSparepartDetails() { return $this->saleInvoiceSparepartDetails; }
    public function setSaleInvoiceSparepartDetails(Collection $saleInvoiceSparepartDetails) { $this->saleInvoiceSparepartDetails = $saleInvoiceSparepartDetails; }
}
