<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\Supplier;
use AppBundle\Entity\Master\PaymentMethod;
use AppBundle\Entity\Master\Account;

/**
 * @ORM\Table(name="transaction_purchase_invoice_downpayment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchaseInvoiceDownpaymentRepository")
 * @UniqueEntity({"taxNumber"})
 */
class PurchaseInvoiceDownpayment extends CodeNumberEntity
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Supplier")
     * @Assert\NotNull()
     */
    private $supplier;
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
     * @ORM\OneToMany(targetEntity="PurchaseInvoiceHeader", mappedBy="purchaseInvoiceDownpayment")
     */
    private $purchaseInvoiceHeaders;
    
    public function __construct()
    {
        $this->purchaseInvoiceHeaders = new ArrayCollection();
    }
    
    public function getCodeNumberConstant()
    {
        return 'PDP';
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

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getSupplier() { return $this->supplier; }
    public function setSupplier(Supplier $supplier = null) { $this->supplier = $supplier; }

    public function getPaymentMethod() { return $this->paymentMethod; }
    public function setPaymentMethod(PaymentMethod $paymentMethod = null) { $this->paymentMethod = $paymentMethod; }

    public function getAccount() { return $this->account; }
    public function setAccount(Account $account = null) { $this->account = $account; }

    public function getPurchaseInvoiceHeaders() { return $this->purchaseInvoiceHeaders; }
    public function setPurchaseInvoiceHeaders(Collection $purchaseInvoiceHeaders) { $this->purchaseInvoiceHeaders = $purchaseInvoiceHeaders; }

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
        
    }
}
