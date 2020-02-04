<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Master\Customer;
use AppBundle\Entity\Master\InspectionItem;

/**
 * @ORM\Table(name="transaction_delivery_inspection_detail")
 * @ORM\Entity
 */
class DeliveryInspectionDetail
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\InspectionItem")
     * @Assert\NotNull()
     */
    private $inspectionItem;
    /**
     * @ORM\ManyToOne(targetEntity="DeliveryInspectionHeader", inversedBy="deliveryInspectionDetails")
     * @Assert\NotNull()
     */
    private $deliveryInspectionHeader;
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }

    public function getInspectionItem() { return $this->inspectionItem; }
    public function setInspectionItem(InspectionItem $inspectionItem = null) { $this->inspectionItem = $inspectionItem; }

    public function getDeliveryInspectionHeader() { return $this->deliveryInspectionHeader; }
    public function setDeliveryInspectionHeader(DeliveryInspectionHeader $deliveryInspectionHeader = null) { $this->deliveryInspectionHeader = $deliveryInspectionHeader; }
}
