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
 * @ORM\Table(name="transaction_delivery_workshop")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\DeliveryWorkshopRepository")
 * @Assert\Expression("
       this.getPurchaseWorkshopHeader() !== null and this.getPurchaseWorkshopHeader().getSaleOrder() !== null and
       this.getReceiveOrder() !== null and this.getReceiveOrder().getPurchaseDeliveryOrder() !== null and this.getReceiveOrder().getPurchaseDeliveryOrder().getSaleOrder() !== null and
       this.getPurchaseWorkshopHeader().getSaleOrder().getId() == this.getReceiveOrder().getPurchaseDeliveryOrder().getSaleOrder().getId()
   ")
 */
class DeliveryWorkshop extends CodeNumberEntity
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
     * @ORM\ManyToOne(targetEntity="PurchaseWorkshopHeader", inversedBy="deliveryWorkshops")
     * @Assert\NotNull()
     */
    private $purchaseWorkshopHeader;
    /**
     * @ORM\OneToOne(targetEntity="ReceiveOrder", inversedBy="deliveryWorkshop")
     * @Assert\NotNull()
     */
    private $receiveOrder;
    /**
     * @ORM\OneToOne(targetEntity="ReceiveWorkshop", mappedBy="deliveryWorkshop")
     */
    private $receiveWorkshop;
    
    public function __construct()
    {
        
    }
    
    public function getCodeNumberConstant()
    {
        return 'DW';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getPurchaseWorkshopHeader() { return $this->purchaseWorkshopHeader; }
    public function setPurchaseWorkshopHeader(PurchaseWorkshopHeader $purchaseWorkshopHeader = null) { $this->purchaseWorkshopHeader = $purchaseWorkshopHeader; }

    public function getReceiveOrder() { return $this->receiveOrder; }
    public function setReceiveOrder(ReceiveOrder $receiveOrder = null) { $this->receiveOrder = $receiveOrder; }

    public function getReceiveWorkshop() { return $this->receiveWorkshop; }
    public function setReceiveWorkshop(ReceiveWorkshop $receiveWorkshop = null) { $this->receiveWorkshop = $receiveWorkshop; }
}
