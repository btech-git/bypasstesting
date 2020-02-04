<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="transaction_sale_invoice_detail_general")
 * @ORM\Entity
 */
class SaleInvoiceDetailGeneral
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
     * @ORM\ManyToOne(targetEntity="SaleInvoiceHeader", inversedBy="saleInvoiceDetailGenerals")
     * @Assert\NotNull()
     */
    private $saleInvoiceHeader;
    
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

    public function getSaleInvoiceHeader() { return $this->saleInvoiceHeader; }
    public function setSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader = null) { $this->saleInvoiceHeader = $saleInvoiceHeader; }

    public function sync()
    {
        $this->total = $this->quantity * $this->unitPrice - $this->discount;
    }
}
