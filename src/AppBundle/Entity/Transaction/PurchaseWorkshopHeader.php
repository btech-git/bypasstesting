<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\Supplier;

/**
 * @ORM\Table(name="transaction_purchase_workshop_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchaseWorkshopHeaderRepository")
 */
class PurchaseWorkshopHeader extends CodeNumberEntity
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
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $quantityOrder;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $subTotal;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $taxNominal;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $taxNominalReplacement;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $grandTotal;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $grandTotalReplacement;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isTax;
    /**
     * @ORM\Column(type="string", length=1)
     * @Assert\NotNull()
     */
    private $approveOrRejectStatus;
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
    private $staffApproveOrReject;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Supplier")
     * @Assert\NotNull()
     */
    private $supplier;
    /**
     * @ORM\OneToOne(targetEntity="SaleOrder", inversedBy="purchaseWorkshopHeader")
     * @Assert\NotNull()
     */
    private $saleOrder;
    /**
     * @ORM\OneToMany(targetEntity="DeliveryWorkshop", mappedBy="purchaseWorkshopHeader")
     */
    private $deliveryWorkshops;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseWorkshopDetail", mappedBy="purchaseWorkshopHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $purchaseWorkshopDetails;
    
    public function __construct()
    {
        $this->purchaseWorkshopDetails = new ArrayCollection();
        $this->deliveryWorkshops = new ArrayCollection();
    }
    
    public function getCodeNumberConstant()
    {
        return 'PW';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getQuantityOrder() { return $this->quantityOrder; }
    public function setQuantityOrder($quantityOrder) { $this->quantityOrder = $quantityOrder; }

    public function getSubTotal() { return $this->subTotal; }
    public function setSubTotal($subTotal) { $this->subTotal = $subTotal; }
    
    public function getTaxNominal() { return $this->taxNominal; }
    public function setTaxNominal($taxNominal) { $this->taxNominal = $taxNominal; }
    
    public function getTaxNominalReplacement() { return $this->taxNominalReplacement; }
    public function setTaxNominalReplacement($taxNominalReplacement) { $this->taxNominalReplacement = $taxNominalReplacement; }
    
    public function getGrandTotal() { return $this->grandTotal; }
    public function setGrandTotal($grandTotal) { $this->grandTotal = $grandTotal; }

    public function getGrandTotalReplacement() { return $this->grandTotalReplacement; }
    public function setGrandTotalReplacement($grandTotalReplacement) { $this->grandTotalReplacement = $grandTotalReplacement; }

    public function getIsTax() { return $this->isTax; }
    public function setIsTax($isTax) { $this->isTax = $isTax; }
    
    public function getApproveOrRejectStatus() { return $this->approveOrRejectStatus; }
    public function setApproveOrRejectStatus($approveOrRejectStatus) { $this->approveOrRejectStatus = $approveOrRejectStatus; }
    
    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getStaffApproveOrReject() { return $this->staffApproveOrReject; }
    public function setStaffApproveOrReject(Staff $staffApproveOrReject = null) { $this->staffApproveOrReject = $staffApproveOrReject; }

    public function getSupplier() { return $this->supplier; }
    public function setSupplier(Supplier $supplier = null) { $this->supplier = $supplier; }

    public function getSaleOrder() { return $this->saleOrder; }
    public function setSaleOrder(SaleOrder $saleOrder = null) { $this->saleOrder = $saleOrder; }

    public function getDeliveryWorkshops() { return $this->deliveryWorkshops; }
    public function setDeliveryWorkshops(Collection $deliveryWorkshops) { $this->deliveryWorkshops = $deliveryWorkshops; }

    public function getPurchaseWorkshopDetails() { return $this->purchaseWorkshopDetails; }
    public function setPurchaseWorkshopDetails(Collection $purchaseWorkshopDetails) { $this->purchaseWorkshopDetails = $purchaseWorkshopDetails; }
}
