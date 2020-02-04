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
 * @ORM\Table(name="transaction_delivery_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\DeliveryOrderRepository")
 */
class DeliveryOrder extends CodeNumberEntity
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
     * @Assert\NotBlank()
     */
    private $destinationAddress;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $destinationPhone;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isNetworkBook;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isSpareTire;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isServiceBook;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isOwnerManual;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isJackHandle;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isLighter;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isToolSet;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isFourHubcap;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isPaintCan;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isCarpetKit;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isSafetyTriangle;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isTwoVehicleKey;
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     */
    private $staffApproval;
    /**
     * @ORM\OneToOne(targetEntity="DeliveryInspectionHeader", inversedBy="deliveryOrder")
     * @Assert\NotNull()
     */
    private $deliveryInspectionHeader;
    
    public function __construct()
    {
    }
    
    public function getCodeNumberConstant()
    {
        return 'SJ';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getDestinationAddress() { return $this->destinationAddress; }
    public function setDestinationAddress($destinationAddress) { $this->destinationAddress = $destinationAddress; }

    public function getDestinationPhone() { return $this->destinationPhone; }
    public function setDestinationPhone($destinationPhone) { $this->destinationPhone = $destinationPhone; }

    public function getIsNetworkBook() { return $this->isNetworkBook; }
    public function setIsNetworkBook($isNetworkBook) { $this->isNetworkBook = $isNetworkBook; }

    public function getIsSpareTire() { return $this->isSpareTire; }
    public function setIsSpareTire($isSpareTire) { $this->isSpareTire = $isSpareTire; }

    public function getIsServiceBook() { return $this->isServiceBook; }
    public function setIsServiceBook($isServiceBook) { $this->isServiceBook = $isServiceBook; }

    public function getIsOwnerManual() { return $this->isOwnerManual; }
    public function setIsOwnerManual($isOwnerManual) { $this->isOwnerManual = $isOwnerManual; }

    public function getIsJackHandle() { return $this->isJackHandle; }
    public function setIsJackHandle($isJackHandle) { $this->isJackHandle = $isJackHandle; }

    public function getIsLighter() { return $this->isLighter; }
    public function setIsLighter($isLighter) { $this->isLighter = $isLighter; }

    public function getIsToolSet() { return $this->isToolSet; }
    public function setIsToolSet($isToolSet) { $this->isToolSet = $isToolSet; }

    public function getIsFourHubcap() { return $this->isFourHubcap; }
    public function setIsFourHubcap($isFourHubcap) { $this->isFourHubcap = $isFourHubcap; }

    public function getIsPaintCan() { return $this->isPaintCan; }
    public function setIsPaintCan($isPaintCan) { $this->isPaintCan = $isPaintCan; }

    public function getIsCarpetKit() { return $this->isCarpetKit; }
    public function setIsCarpetKit($isCarpetKit) { $this->isCarpetKit = $isCarpetKit; }

    public function getIsSafetyTriangle() { return $this->isSafetyTriangle; }
    public function setIsSafetyTriangle($isSafetyTriangle) { $this->isSafetyTriangle = $isSafetyTriangle; }

    public function getIsTwoVehicleKey() { return $this->isTwoVehicleKey; }
    public function setIsTwoVehicleKey($isTwoVehicleKey) { $this->isTwoVehicleKey = $isTwoVehicleKey; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getStaffApproval() { return $this->staffApproval; }
    public function setStaffApproval(Staff $staffApproval = null) { $this->staffApproval = $staffApproval; }

    public function getDeliveryInspectionHeader() { return $this->deliveryInspectionHeader; }
    public function setDeliveryInspectionHeader(DeliveryInspectionHeader $deliveryInspectionHeader = null) { $this->deliveryInspectionHeader = $deliveryInspectionHeader; }
}
