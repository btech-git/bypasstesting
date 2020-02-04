<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\Customer;
use AppBundle\Entity\Master\PaymentMethod;
use AppBundle\Entity\Master\Account;

/**
 * @ORM\Table(name="transaction_sale_invoice_downpayment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\SaleInvoiceDownpaymentRepository")
 * @UniqueEntity({"taxNumber"})
 */
class SaleInvoiceDownpayment extends CodeNumberEntity
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
     * @Assert\NotBlank() @Assert\Length(min=16, max=16)
     */
    private $taxNumber;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $amount;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalPayment;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $remaining;
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
     * @ORM\ManyToOne(targetEntity="SaleOrder", inversedBy="saleInvoiceDownpayments")
     * @Assert\NotNull()
     */
    private $saleOrder;
    /**
     * @ORM\OneToMany(targetEntity="SalePaymentHeader", mappedBy="saleInvoiceDownpayment")
     */
    private $salePaymentHeaders;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Account")
     * @Assert\NotNull()
     */
    private $account;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\PaymentMethod")
     * @Assert\NotNull()
     */
    private $paymentMethod;
    /**
     * @ORM\OneToMany(targetEntity="SaleInvoiceDetailUnitDownpayment", mappedBy="saleInvoiceDownpayment")
     */
    private $saleInvoiceDetailUnitDownpayments;
    
    public function __construct()
    {        
        $this->saleInvoiceDetailUnitDownpayments = new ArrayCollection();
        $this->salePaymentHeaders = new ArrayCollection();
    }
    
    public function getCodeNumberConstant()
    {
        return 'SDP';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getTaxNumber() { return $this->taxNumber; }
    public function setTaxNumber($taxNumber) { $this->taxNumber = $taxNumber; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getAmount() { return $this->amount; }
    public function setAmount($amount) { $this->amount = $amount; }

    public function getTotalPayment() { return $this->totalPayment; }
    public function setTotalPayment($totalPayment) { $this->totalPayment = $totalPayment; }

    public function getRemaining() { return $this->remaining; }
    public function setRemaining($remaining) { $this->remaining = $remaining; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getCustomer() { return $this->customer; }
    public function setCustomer(Customer $customer = null) { $this->customer = $customer; }

    public function getSaleOrder() { return $this->saleOrder; }
    public function setSaleOrder(SaleOrder $saleOrder = null) { $this->saleOrder = $saleOrder; }

    public function getSalePaymentHeaders() { return $this->salePaymentHeaders; }
    public function setSalePaymentHeaders(Collection $salePaymentHeaders) { $this->salePaymentHeaders = $salePaymentHeaders; }

    public function getPaymentMethod() { return $this->paymentMethod; }
    public function setPaymentMethod(PaymentMethod $paymentMethod = null) { $this->paymentMethod = $paymentMethod; }

    public function getAccount() { return $this->account; }
    public function setAccount(Account $account = null) { $this->account = $account; }

    public function getSaleInvoiceDetailUnitDownpayments() { return $this->saleInvoiceDetailUnitDownpayments; }
    public function setSaleInvoiceDetailUnitDownpayments(Collection $saleInvoiceDetailUnitDownpayments) { $this->saleInvoiceDetailUnitDownpayments = $saleInvoiceDetailUnitDownpayments; }

    public function getFormattedTaxNumber()
    {
        $part1 = substr($this->taxNumber, 0, 3);
        $part2 = substr($this->taxNumber, 3, 3);
        $part3 = substr($this->taxNumber, 6, 2);
        $part4 = substr($this->taxNumber, 8, 8);
        return $part1 . '.' . $part2 . '-' . $part3 . '.' . $part4;
    }

    public function sync()
    {
        $totalPayment = '0.00';
        if ($this->salePaymentHeaders !== null) {
            foreach ($this->salePaymentHeaders as $salePaymentHeader) {
                $salePaymentHeader->sync();
                $totalPayment = $salePaymentHeader->getTotalAmount();
            }
        }
        $this->totalPayment = $totalPayment;
        
        $this->remaining = $this->amount - $this->totalPayment;
        
    }
}
