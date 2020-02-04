<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Master\Account;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_expense_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\ExpenseHeaderRepository")
 */
class ExpenseHeader extends CodeNumberEntity
{
    const TRANSACTION_TYPE_UNIT = 'unit';
    const TRANSACTION_TYPE_GENERAL = 'umum';
    
    /**
     * @ORM\Column(name="id", type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(name="transaction_date", type="date")
     * @Assert\NotNull() @Assert\Date()
     */
    private $transactionDate;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $transactionType;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $objectiveReason;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $chequeNumber;
    /**
     * @ORM\Column(name="note", type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Account")
     * @Assert\NotNull()
     */
    private $account;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseDeliveryOrder", inversedBy="expenseHeaders")
     */
    private $purchaseDeliveryOrder;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     * @Assert\NotNull()
     */
    private $staff;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     */
    private $staffApproval;
    /**
     * @ORM\OneToMany(targetEntity="ExpenseDetail", mappedBy="expenseHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $expenseDetails;
    
    public function __construct()
    {
        $this->expenseDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'EXP'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }

    public function getTransactionType() { return $this->transactionType; }
    public function setTransactionType($transactionType) { $this->transactionType = $transactionType; }

    public function getObjectiveReason() { return $this->objectiveReason; }
    public function setObjectiveReason($objectiveReason) { $this->objectiveReason = $objectiveReason; }

    public function getChequeNumber() { return $this->chequeNumber; }
    public function setChequeNumber($chequeNumber) { $this->chequeNumber = $chequeNumber; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }
    
    public function getAccount() { return $this->account; }
    public function setAccount(Account $account = null) { $this->account = $account; }
    
    public function getStaff() { return $this->staff; }
    public function setStaff(Staff $staff = null) { $this->staff = $staff; }
    
    public function getStaffApproval() { return $this->staffApproval; }
    public function setStaffApproval(Staff $staffApproval = null) { $this->staffApproval = $staffApproval; }
    
    public function getPurchaseDeliveryOrder() { return $this->purchaseDeliveryOrder; }
    public function setPurchaseDeliveryOrder(PurchaseDeliveryOrder $purchaseDeliveryOrder = null) { $this->purchaseDeliveryOrder = $purchaseDeliveryOrder; }
    
    public function getExpenseDetails() { return $this->expenseDetails; }
    public function setExpenseDetails(Collection $expenseDetails) { $this->expenseDetails = $expenseDetails; }
    
    public function getTotalAmount()
    {
        $total = 0.00;
        foreach ($this->expenseDetails as $expenseDetail) {
            $total += $expenseDetail->getAmount();
        }
        
        return $total;
    }
}
