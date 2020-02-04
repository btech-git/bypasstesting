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
 * @ORM\Table(name="transaction_delivery_inspection_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\DeliveryInspectionHeaderRepository")
 */
class DeliveryInspectionHeader extends CodeNumberEntity
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
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isVehicleComplete;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isVehicleIncomplete;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isBodyBuilderExecuted;
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
     * @ORM\OneToOne(targetEntity="ReceiveOrder", inversedBy="deliveryInspectionHeader")
     * @Assert\NotNull()
     */
    private $receiveOrder;
    /**
     * @ORM\OneToMany(targetEntity="DeliveryInspectionDetail", mappedBy="deliveryInspectionHeader")
     */
    private $deliveryInspectionDetails;
    /**
     * @ORM\OneToOne(targetEntity="DeliveryOrder", mappedBy="deliveryInspectionHeader")
     */
    private $deliveryOrder;
    
    public function __construct()
    {
        $this->deliveryInspectionDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant()
    {
        return 'PDI';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getIsVehicleComplete() { return $this->isVehicleComplete; }
    public function setIsVehicleComplete($isVehicleComplete) { $this->isVehicleComplete = $isVehicleComplete; }

    public function getIsVehicleIncomplete() { return $this->isVehicleIncomplete; }
    public function setIsVehicleIncomplete($isVehicleIncomplete) { $this->isVehicleIncomplete = $isVehicleIncomplete; }

    public function getIsBodyBuilderExecuted() { return $this->isBodyBuilderExecuted; }
    public function setIsBodyBuilderExecuted($isBodyBuilderExecuted) { $this->isBodyBuilderExecuted = $isBodyBuilderExecuted; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getReceiveOrder() { return $this->receiveOrder; }
    public function setReceiveOrder(ReceiveOrder $receiveOrder = null) { $this->receiveOrder = $receiveOrder; }

    public function getDeliveryInspectionDetails() { return $this->deliveryInspectionDetails; }
    public function setDeliveryInspectionDetails(Collection $deliveryInspectionDetails) { $this->deliveryInspectionDetails = $deliveryInspectionDetails; }

    public function getDeliveryOrder() { return $this->deliveryOrder; }
    public function setDeliveryOrder(DeliveryOrder $deliveryOrder = null) { $this->deliveryOrder = $deliveryOrder; }
}
