<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Master\VehicleModel;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_sale_invoice_detail_unit")
 * @ORM\Entity
 */
class SaleInvoiceDetailUnit
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $vehicleChassisNumber;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $vehicleMachineNumber;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $quantity;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $unitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $total;
    /**
     * @ORM\ManyToOne(targetEntity="SaleInvoiceHeader", inversedBy="saleInvoiceDetailUnits")
     * @Assert\NotNull()
     */
    private $saleInvoiceHeader;
    /**
     * @ORM\OneToOne(targetEntity="ReceiveOrder", inversedBy="saleInvoiceDetailUnit")
     * @Assert\NotNull()
     */
    private $receiveOrder;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\VehicleModel", inversedBy="saleInvoiceDetailUnits")
     * @Assert\NotNull()
     */
    private $vehicleModel;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff", inversedBy="saleInvoiceDetailUnits")
     * @Assert\NotNull()
     */
    private $staffSalesman;
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }

    public function getVehicleChassisNumber() { return $this->vehicleChassisNumber; }
    public function setVehicleChassisNumber($vehicleChassisNumber) { $this->vehicleChassisNumber = $vehicleChassisNumber; }

    public function getVehicleMachineNumber() { return $this->vehicleMachineNumber; }
    public function setVehicleMachineNumber($vehicleMachineNumber) { $this->vehicleMachineNumber = $vehicleMachineNumber; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getSaleInvoiceHeader() { return $this->saleInvoiceHeader; }
    public function setSaleInvoiceHeader(SaleinvoiceHeader $saleInvoiceHeader = null) { $this->saleInvoiceHeader = $saleInvoiceHeader; }

    public function getReceiveOrder() { return $this->receiveOrder; }
    public function setReceiveOrder(ReceiveOrder $receiveOrder = null) { $this->receiveOrder = $receiveOrder; }

    public function getVehicleModel() { return $this->vehicleModel; }
    public function setVehicleModel(VehicleModel $vehicleModel = null) { $this->vehicleModel = $vehicleModel; }

    public function getStaffSalesman() { return $this->staffSalesman; }
    public function setStaffSalesman(Staff $staffSalesman = null) { $this->staffSalesman = $staffSalesman; }

    public function sync()
    {
        $this->total = $this->quantity * $this->unitPrice;
    }
}
