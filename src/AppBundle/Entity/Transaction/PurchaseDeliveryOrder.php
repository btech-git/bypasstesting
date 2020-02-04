<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\VehicleModel;
use AppBundle\Entity\Master\Supplier;

/**
 * @ORM\Table(name="transaction_purchase_delivery_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchaseDeliveryOrderRepository")
 */
class PurchaseDeliveryOrder extends CodeNumberEntity
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
     * @Assert\Date()
     */
    private $dueDate;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $reference;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(2010)
     */
    private $vehicleProductionYear;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $vehicleChassisNumber;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $vehicleMachineNumber;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $vehicleDescription;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isStock;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Supplier")
     */
    private $supplier;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\VehicleModel", inversedBy="purchaseDeliveryOrders")
     * @Assert\NotNull()
     */
    private $vehicleModel;
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
     * @ORM\ManyToOne(targetEntity="SaleOrder", inversedBy="purchaseDeliveryOrders")
     */
    private $saleOrder;
    /**
     * @ORM\OneToOne(targetEntity="ReceiveOrder", mappedBy="purchaseDeliveryOrder")
     */
    private $receiveOrder;
    /**
     * @ORM\OneToOne(targetEntity="PurchaseInvoiceDetailUnit", mappedBy="purchaseDeliveryOrder")
     */
    private $purchaseInvoiceDetailUnit;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseInvoiceHeader", mappedBy="purchaseDeliveryOrder")
     */
    private $purchaseInvoiceHeaders;
    /**
     * @ORM\OneToMany(targetEntity="DepositHeader", mappedBy="purchaseDeliveryOrder")
     */
    private $depositHeaders;
    /**
     * @ORM\OneToMany(targetEntity="ExpenseHeader", mappedBy="purchaseDeliveryOrder")
     */
    private $expenseHeaders;
    /**
     * @ORM\OneToMany(targetEntity="JournalVoucherHeader", mappedBy="purchaseDeliveryOrder")
     */
    private $journalVoucherHeaders;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Report\JournalLedger", mappedBy="purchaseDeliveryOrder")
     */
    private $journalLedgers;
    
    public function __construct()
    {
        $this->purchaseInvoiceHeaders = new ArrayCollection();  
        $this->depositHeaders = new ArrayCollection();      
        $this->expenseHeaders = new ArrayCollection();  
        $this->journalVoucherHeaders = new ArrayCollection();  
        $this->journalLedgers = new ArrayCollection();   
    }
    
    public function getCodeNumberConstant()
    {
        return 'PDO';
    }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getDueDate() { return $this->dueDate; }
    public function setDueDate($dueDate) { $this->dueDate = $dueDate; }

    public function getReference() { return $this->reference; }
    public function setReference($reference) { $this->reference = $reference; }

    public function getVehicleProductionYear() { return $this->vehicleProductionYear; }
    public function setVehicleProductionYear($vehicleProductionYear) { $this->vehicleProductionYear = $vehicleProductionYear; }

    public function getVehicleChassisNumber() { return $this->vehicleChassisNumber; }
    public function setVehicleChassisNumber($vehicleChassisNumber) { $this->vehicleChassisNumber = $vehicleChassisNumber; }

    public function getVehicleMachineNumber() { return $this->vehicleMachineNumber; }
    public function setVehicleMachineNumber($vehicleMachineNumber) { $this->vehicleMachineNumber = $vehicleMachineNumber; }

    public function getVehicleDescription() { return $this->vehicleDescription; }
    public function setVehicleDescription($vehicleDescription) { $this->vehicleDescription = $vehicleDescription; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getIsStock() { return $this->isStock; }
    public function setIsStock($isStock) { $this->isStock = $isStock; }

    public function getVehicleModel() { return $this->vehicleModel; }
    public function setVehicleModel(VehicleModel $vehicleModel = null) { $this->vehicleModel = $vehicleModel; }

    public function getSupplier() { return $this->supplier; }
    public function setSupplier(Supplier $supplier = null) { $this->supplier = $supplier; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getSaleOrder() { return $this->saleOrder; }
    public function setSaleOrder(SaleOrder $saleOrder = null) { $this->saleOrder = $saleOrder; }

    public function getPurchaseInvoiceDetailUnit() { return $this->purchaseInvoiceDetailUnit; }
    public function setPurchaseInvoiceDetailUnit(PurchaseInvoiceDetailUnit $purchaseInvoiceDetailUnit = null) { $this->purchaseInvoiceDetailUnit = $purchaseInvoiceDetailUnit; }

    public function getReceiveOrder() { return $this->receiveOrder; }
    public function setReceiveOrder(ReceiveOrder $receiveOrder = null) { $this->receiveOrder = $receiveOrder; }
    
    public function getPurchaseInvoiceHeaders() { return $this->purchaseInvoiceHeaders; }
    public function setPurchaseInvoiceHeaders(Collection $purchaseInvoiceHeaders) { $this->purchaseInvoiceHeaders = $purchaseInvoiceHeaders; }

    public function getDepositHeaders() { return $this->depositHeaders; }
    public function setDepositHeaders(Collection $depositHeaders) { $this->depositHeaders = $depositHeaders; }

    public function getExpenseHeaders() { return $this->expenseHeaders; }
    public function setExpenseHeaders(Collection $expenseHeaders) { $this->expenseHeaders = $expenseHeaders; }

    public function getJournalVoucherHeaders() { return $this->journalVoucherHeaders; }
    public function setJournalVoucherHeaders(Collection $journalVoucherHeaders) { $this->journalVoucherHeaders = $journalVoucherHeaders; }

    public function getJournalLedgers() { return $this->journalLedgers; }
    public function setJournalLedgers(Collection $journalLedgers) { $this->journalLedgers = $journalLedgers; }

    public function sync()
    {
        if ($this->isStock) {
            $this->saleOrder = null;
        }
    }
}
