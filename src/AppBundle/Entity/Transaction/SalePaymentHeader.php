<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberPaymentEntity;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_sale_payment_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\SalePaymentHeaderRepository")
 */
class SalePaymentHeader extends CodeNumberPaymentEntity
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
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $totalAmount;
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
     * @ORM\ManyToOne(targetEntity="SaleInvoiceHeader", inversedBy="salePaymentHeaders")
     */
    private $saleInvoiceHeader;
    /**
     * @ORM\ManyToOne(targetEntity="SaleInvoiceDownpayment", inversedBy="salePaymentHeaders")
     */
    private $saleInvoiceDownpayment;
    /**
     * @ORM\OneToMany(targetEntity="SalePaymentDetail", mappedBy="salePaymentHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $salePaymentDetails;
    
    public function __construct()
    {
        $this->salePaymentDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant()
    {
        return 'SPY';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getTotalAmount() { return $this->totalAmount; }
    public function setTotalAmount($totalAmount) { $this->totalAmount = $totalAmount; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getSaleInvoiceHeader() { return $this->saleInvoiceHeader; }
    public function setSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader = null) { $this->saleInvoiceHeader = $saleInvoiceHeader; }

    public function getSaleInvoiceDownpayment() { return $this->saleInvoiceDownpayment; }
    public function setSaleInvoiceDownpayment(SaleInvoiceDownpayment $saleInvoiceDownpayment = null) { $this->saleInvoiceDownpayment = $saleInvoiceDownpayment; }

    public function getSalePaymentDetails() { return $this->salePaymentDetails; }
    public function setSalePaymentDetails(Collection $salePaymentDetails) { $this->salePaymentDetails = $salePaymentDetails; }
    
    public function sync()
    {
        $totalAmount = '0.00';
        foreach ($this->salePaymentDetails as $salePaymentDetail) {
            $totalAmount += $salePaymentDetail->getAmount();
        }
        $this->totalAmount = $totalAmount;
    }
}
