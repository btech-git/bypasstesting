<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="transaction_sale_invoice_sparepart_detail")
 * @ORM\Entity
 */
class SaleInvoiceSparepartDetail
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $itemCode;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $itemName;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $supplySysNumber;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $supplySlipNumber;
    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $quantity;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $itemPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $servicePrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $materialPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $sparepartPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $outsourcePrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $oilPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $accessoryPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $packagePrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $souvenirPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $serviceDiscount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $materialDiscount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $sparepartDiscount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $outsourceDiscount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $oilDiscount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $accessoryDiscount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $packageDiscount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $souvenirDiscount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $totalBeforeTax;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalTax;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $totalAfterTax;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $saleAmount;
    /**
     * @ORM\ManyToOne(targetEntity="SaleInvoiceSparepartHeader", inversedBy="saleInvoiceSparepartDetails")
     * @Assert\NotNull()
     */
    private $saleInvoiceSparepartHeader;
    
    public function __construct()
    {
    }

    public function getId() { return $this->id; }

    public function getItemCode() { return $this->itemCode; }
    public function setItemCode($itemCode) { $this->itemCode = $itemCode; }

    public function getItemName() { return $this->itemName; }
    public function setItemName($itemName) { $this->itemName = $itemName; }

    public function getSupplySysNumber() { return $this->supplySysNumber; }
    public function setSupplySysNumber($supplySysNumber) { $this->supplySysNumber = $supplySysNumber; }

    public function getSupplySlipNumber() { return $this->supplySlipNumber; }
    public function setSupplySlipNumber($supplySlipNumber) { $this->supplySlipNumber = $supplySlipNumber; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getItemPrice() { return $this->itemPrice; }
    public function setItemPrice($itemPrice) { $this->itemPrice = $itemPrice; }

    public function getServicePrice() { return $this->servicePrice; }
    public function setServicePrice($servicePrice) { $this->servicePrice = $servicePrice; }

    public function getMaterialPrice() { return $this->materialPrice; }
    public function setMaterialPrice($materialPrice) { $this->materialPrice = $materialPrice; }

    public function getSparepartPrice() { return $this->sparepartPrice; }
    public function setSparepartPrice($sparepartPrice) { $this->sparepartPrice = $sparepartPrice; }

    public function getOutsourcePrice() { return $this->outsourcePrice; }
    public function setOutsourcePrice($outsourcePrice) { $this->outsourcePrice = $outsourcePrice; }

    public function getOilPrice() { return $this->oilPrice; }
    public function setOilPrice($oilPrice) { $this->oilPrice = $oilPrice; }

    public function getAccessoryPrice() { return $this->accessoryPrice; }
    public function setAccessoryPrice($accessoryPrice) { $this->accessoryPrice = $accessoryPrice; }

    public function getPackagePrice() { return $this->packagePrice; }
    public function setPackagePrice($packagePrice) { $this->packagePrice = $packagePrice; }

    public function getSouvenirPrice() { return $this->souvenirPrice; }
    public function setSouvenirPrice($souvenirPrice) { $this->souvenirPrice = $souvenirPrice; }

    public function getServiceDiscount() { return $this->serviceDiscount; }
    public function setServiceDiscount($serviceDiscount) { $this->serviceDiscount = $serviceDiscount; }

    public function getMaterialDiscount() { return $this->materialDiscount; }
    public function setMaterialDiscount($materialDiscount) { $this->materialDiscount = $materialDiscount; }

    public function getSparepartDiscount() { return $this->sparepartDiscount; }
    public function setSparepartDiscount($sparepartDiscount) { $this->sparepartDiscount = $sparepartDiscount; }

    public function getOutsourceDiscount() { return $this->outsourceDiscount; }
    public function setOutsourceDiscount($outsourceDiscount) { $this->outsourceDiscount = $outsourceDiscount; }

    public function getOilDiscount() { return $this->oilDiscount; }
    public function setOilDiscount($oilDiscount) { $this->oilDiscount = $oilDiscount; }

    public function getAccessoryDiscount() { return $this->accessoryDiscount; }
    public function setAccessoryDiscount($accessoryDiscount) { $this->accessoryDiscount = $accessoryDiscount; }

    public function getPackageDiscount() { return $this->packageDiscount; }
    public function setPackageDiscount($packageDiscount) { $this->packageDiscount = $packageDiscount; }

    public function getSouvenirDiscount() { return $this->souvenirDiscount; }
    public function setSouvenirDiscount($souvenirDiscount) { $this->souvenirDiscount = $souvenirDiscount; }

    public function getTotalBeforeTax() { return $this->totalBeforeTax; }
    public function setTotalBeforeTax($totalBeforeTax) { $this->totalBeforeTax = $totalBeforeTax; }

    public function getTotalTax() { return $this->totalTax; }
    public function setTotalTax($totalTax) { $this->totalTax = $totalTax; }

    public function getTotalAfterTax() { return $this->totalAfterTax; }
    public function setTotalAfterTax($totalAfterTax) { $this->totalAfterTax = $totalAfterTax; }

    public function getSaleAmount() { return $this->saleAmount; }
    public function setSaleAmount($saleAmount) { $this->saleAmount = $saleAmount; }

    public function getSaleInvoiceSparepartHeader() { return $this->saleInvoiceSparepartHeader; }
    public function setSaleInvoiceSparepartHeader(SaleInvoiceSparepartHeader $saleInvoiceSparepartHeader = null) { $this->saleInvoiceSparepartHeader = $saleInvoiceSparepartHeader; }
}

