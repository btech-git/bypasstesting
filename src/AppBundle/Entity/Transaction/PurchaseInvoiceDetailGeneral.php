<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_purchase_invoice_detail_general")
 * @ORM\Entity
 */
class PurchaseInvoiceDetailGeneral
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $itemName;
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
     * @ORM\ManyToOne(targetEntity="PurchaseInvoiceHeader", inversedBy="purchaseInvoiceDetailGenerals")
     * @Assert\NotNull()
     */
    private $purchaseInvoiceHeader;
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }

    public function getItemName() { return $this->itemName; }
    public function setItemName($itemName) { $this->itemName = $itemName; }

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

    public function sync()
    {
        $this->total = $this->quantity * $this->unitPrice - $this->discount;
    }
}
