<?php

namespace AppBundle\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\User;

/**
 * @ORM\Entity
 * @UniqueEntity("email")
 */
class Staff extends User
{
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     */
    private $position;
    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull() @Assert\Date()
     */
    private $joinDate;
    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank() @Assert\Email()
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $address;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $phone;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isActive = true;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction\SaleInvoiceDetailUnit", mappedBy="staffSalesman")
     */
    private $saleInvoiceDetailUnits;
    
    public function __construct()
    {
        $this->saleInvoiceDetailUnits = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name;
    }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getPosition() { return $this->position; }
    public function setPosition($position) { $this->position = $position; }
    
    public function getJoinDate() { return $this->joinDate; }
    public function setJoinDate($joinDate) { $this->joinDate = $joinDate; }
    
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    
    public function getAddress() { return $this->address; }
    public function setAddress($address) { $this->address = $address; }
    
    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }
    
    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }
    
    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    public function getSaleInvoiceDetailUnits() { return $this->saleInvoiceDetailUnits; }
    public function setSaleInvoiceDetailUnits(Collection $saleInvoiceDetailUnits) { $this->saleInvoiceDetailUnits = $saleInvoiceDetailUnits; }
    
    public function getRoles()
    {
        $defaultRoles = array_merge(parent::getRoles(), array('ROLE_STAFF'));
        $assignedRoles = array_map(function($userRole) { return $userRole->getRole(); }, $this->getUserRoles()->toArray());
        $roles = array_unique(array_merge($defaultRoles, $assignedRoles));
        
        return $roles;
    }
}
