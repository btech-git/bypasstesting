<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="transaction_purchase_invoice_detail_unit")
 * @ORM\Entity
 */
class PurchaseInvoiceDetailUnit
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $vehicleChassisNumber;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $vehicleMachineNumber;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $quantity;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $unitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $total;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseInvoiceHeader", inversedBy="purchaseInvoiceDetailUnits")
     * @Assert\NotNull()
     */
    private $purchaseInvoiceHeader;
    /**
     * @ORM\OneToOne(targetEntity="PurchaseDeliveryOrder", inversedBy="purchaseInvoiceDetailUnit")
     * @Assert\NotNull()
     */
    private $purchaseDeliveryOrder;
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }

    public function getVehicleChassisNumber() { return $this->vehicleChassisNumber; }
    public function setVehicleChassisNumber($vehicleChassisNumber) { $this->vehicleChassisNumber = $vehicleChassisNumber; }

    public function getVehicleMachineNumber() { return $this->vehicleMachineNumber; }
    public function setVehicleMachineNumber($vehicleMachineNumber) { $this->vehicleMachineNumber = $vehicleMachineNumber; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getDiscount() { return $this->discount; }
    public function setDiscount($discount) { $this->discount = $discount; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getPurchaseInvoiceHeader() { return $this->purchaseInvoiceHeader; }
    public function setPurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader = null) { $this->purchaseInvoiceHeader = $purchaseInvoiceHeader; }

    public function getPurchaseDeliveryOrder() { return $this->purchaseDeliveryOrder; }
    public function setPurchaseDeliveryOrder(PurchaseDeliveryOrder $purchaseDeliveryOrder = null) { $this->purchaseDeliveryOrder = $purchaseDeliveryOrder; }

    public function sync()
    {
        $this->total = $this->quantity * $this->unitPrice - $this->discount;
    }
}
