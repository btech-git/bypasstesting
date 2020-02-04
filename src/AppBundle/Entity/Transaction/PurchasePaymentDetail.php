<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Master\Account;
use AppBundle\Entity\Master\PaymentMethod;

/**
 * @ORM\Table(name="transaction_purchase_payment_detail")
 * @ORM\Entity
 */
class PurchasePaymentDetail
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
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $memo;
    /**
     * @ORM\ManyToOne(targetEntity="PurchasePaymentHeader", inversedBy="purchasePaymentDetails")
     * @Assert\NotNull()
     */
    private $purchasePaymentHeader;
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
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }

    public function getAmount() { return $this->amount; }
    public function setAmount($amount) { $this->amount = $amount; }

    public function getMemo() { return $this->memo; }
    public function setMemo($memo) { $this->memo = $memo; }

    public function getPaymentMethod() { return $this->paymentMethod; }
    public function setPaymentMethod(PaymentMethod $paymentMethod = null) { $this->paymentMethod = $paymentMethod; }

    public function getAccount() { return $this->account; }
    public function setAccount(Account $account = null) { $this->account = $account; }

    public function getPurchasePaymentHeader() { return $this->purchasePaymentHeader; }
    public function setPurchasePaymentHeader(PurchasePaymentHeader $purchasePaymentHeader = null) { $this->purchasePaymentHeader = $purchasePaymentHeader; }
}
