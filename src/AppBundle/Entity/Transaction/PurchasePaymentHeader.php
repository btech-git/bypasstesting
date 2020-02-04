<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberAccountEntity;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_purchase_payment_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchasePaymentHeaderRepository")
 * @Assert\Expression("(this.getPurchaseInvoiceHeader().getRemaining() >= 0)", message = "Remaining must be greater or equal to 0")
 */
class PurchasePaymentHeader extends CodeNumberAccountEntity
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
     * @ORM\ManyToOne(targetEntity="PurchaseInvoiceHeader", inversedBy="purchasePaymentHeaders")
     * @Assert\NotNull()
     */
    private $purchaseInvoiceHeader;
    /**
     * @ORM\OneToMany(targetEntity="PurchasePaymentDetail", mappedBy="purchasePaymentHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $purchasePaymentDetails;
    
    public function __construct()
    {
        $this->purchasePaymentDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant()
    {
        return 'PPY';
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

    public function getPurchaseInvoiceHeader() { return $this->purchaseInvoiceHeader; }
    public function setPurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader = null) { $this->purchaseInvoiceHeader = $purchaseInvoiceHeader; }

    public function getPurchasePaymentDetails() { return $this->purchasePaymentDetails; }
    public function setPurchasePaymentDetails(Collection $purchasePaymentDetails) { $this->purchasePaymentDetails = $purchasePaymentDetails; }

    public function sync()
    {
        $totalAmount = '0.00';
        foreach ($this->purchasePaymentDetails as $purchasePaymentDetail) {
            $totalAmount += $purchasePaymentDetail->getAmount();
        }
        $this->totalAmount = $totalAmount;
    }
}
