<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="transaction_sale_invoice_detail_unit_downpayment")
 * @ORM\Entity
 */
class SaleInvoiceDetailUnitDownpayment
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $amount;
    /**
     * @ORM\ManyToOne(targetEntity="SaleInvoiceHeader", inversedBy="saleInvoiceDetailUnitDownpayments")
     * @Assert\NotNull()
     */
    private $saleInvoiceHeader;
    /**
     * @ORM\ManyToOne(targetEntity="SaleInvoiceDownpayment", inversedBy="saleInvoiceDetailUnitDownpayments")
     * @Assert\NotNull()
     */
    private $saleInvoiceDownpayment;
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }

    public function getAmount() { return $this->amount; }
    public function setAmount($amount) { $this->amount = $amount; }

    public function getSaleInvoiceHeader() { return $this->saleInvoiceHeader; }
    public function setSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader = null) { $this->saleInvoiceHeader = $saleInvoiceHeader; }

    public function getSaleInvoiceDownpayment() { return $this->saleInvoiceDownpayment; }
    public function setSaleInvoiceDownpayment(SaleInvoiceDownpayment $saleInvoiceDownpayment = null) { $this->saleInvoiceDownpayment = $saleInvoiceDownpayment; }
}
