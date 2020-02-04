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
 * @ORM\Table(name="transaction_receive_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\ReceiveOrderRepository")
 */
class ReceiveOrder extends CodeNumberEntity
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
     * @ORM\Column(type="date")
     * @Assert\NotNull() @Assert\Date()
     */
    private $deliveryDate;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $serviceBookNumber;
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
     * @ORM\OneToOne(targetEntity="PurchaseDeliveryOrder", inversedBy="receiveOrder")
     * @Assert\NotNull()
     */
    private $purchaseDeliveryOrder;
    /**
     * @ORM\OneToOne(targetEntity="DeliveryWorkshop", mappedBy="receiveOrder")
     */
    private $deliveryWorkshop;
    /**
     * @ORM\OneToOne(targetEntity="DeliveryInspectionHeader", mappedBy="receiveOrder")
     */
    private $deliveryInspectionHeader;
    /**
     * @ORM\OneToOne(targetEntity="SaleInvoiceDetailUnit", mappedBy="receiveOrder")
     */
    private $saleInvoiceDetailUnit;
    
    public function __construct()
    {
        $this->deliveryWorkshops = new ArrayCollection();
    }
    
    public function getCodeNumberConstant()
    {
        return 'RCV';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getDeliveryDate() { return $this->deliveryDate; }
    public function setDeliveryDate($deliveryDate) { $this->deliveryDate = $deliveryDate; }

    public function getServiceBookNumber() { return $this->serviceBookNumber; }
    public function setServiceBookNumber($serviceBookNumber) { $this->serviceBookNumber = $serviceBookNumber; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getPurchaseDeliveryOrder() { return $this->purchaseDeliveryOrder; }
    public function setPurchaseDeliveryOrder(PurchaseDeliveryOrder $purchaseDeliveryOrder = null) { $this->purchaseDeliveryOrder = $purchaseDeliveryOrder; }

    public function getDeliveryWorkshop() { return $this->deliveryWorkshop; }
    public function setDeliveryWorkshop(DeliveryWorkshop $deliveryWorkshop = null) { $this->deliveryWorkshop = $deliveryWorkshop; }

    public function getDeliveryInspectionHeader() { return $this->deliveryInspectionHeader; }
    public function setDeliveryInspectionHeader(DeliveryInspectionHeader $deliveryInspectionHeader = null) { $this->deliveryInspectionHeader = $deliveryInspectionHeader; }

    public function getSaleInvoiceDetailUnit() { return $this->saleInvoiceDetailUnit; }
    public function setSaleInvoiceDetailUnit(SaleInvoiceDetailUnit $saleInvoiceDetailUnit = null) { $this->saleInvoiceDetailUnit = $saleInvoiceDetailUnit; }
}
