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
 * @ORM\Table(name="transaction_receive_workshop")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\ReceiveWorkshopRepository")
 */
class ReceiveWorkshop extends CodeNumberEntity
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
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $supplierDeliveryNumber;
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
     * @ORM\OneToOne(targetEntity="DeliveryWorkshop", inversedBy="receiveWorkshop")
     * @Assert\NotNull()
     */
    private $deliveryWorkshop;
    /**
     * @ORM\OneToOne(targetEntity="PurchaseInvoiceHeader", mappedBy="receiveWorkshop")
     */
    private $purchaseInvoiceHeader;
    
    public function __construct()
    {
    }
    
    public function getCodeNumberConstant()
    {
        return 'RW';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getSupplierDeliveryNumber() { return $this->supplierDeliveryNumber; }
    public function setSupplierDeliveryNumber($supplierDeliveryNumber) { $this->supplierDeliveryNumber = $supplierDeliveryNumber; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getPurchaseInvoiceHeader() { return $this->purchaseInvoiceHeader; }
    public function setPurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader = null) { $this->purchaseInvoiceHeader = $purchaseInvoiceHeader; }

    public function getDeliveryWorkshop() { return $this->deliveryWorkshop; }
    public function setDeliveryWorkshop(DeliveryWorkshop $deliveryWorkshop = null) { $this->deliveryWorkshop = $deliveryWorkshop; }
}
