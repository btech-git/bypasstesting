<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_finance_company") @ORM\Entity
 * @UniqueEntity("name")
 * @UniqueEntity("email")
 */
class FinanceCompany
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $branchName;
    
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $address;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $phone;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $fax;
    
    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $contactPerson;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $contactPersonMobilePhone;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $directorName;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $directorMobilePhone;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isBank;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isActive = true;
    
    public function __construct()
    {
    }
    
    public function __toString()
    {
        return $this->name;
    }
    
    public function getId() { return $this->id; }
    
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getBranchName() { return $this->branchName; }
    public function setBranchName($branchName) { $this->branchName = $branchName; }
    
    public function getAddress() { return $this->address; }
    public function setAddress($address) { $this->address = $address; }
    
    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }
    
    public function getFax() { return $this->fax; }
    public function setFax($fax) { $this->fax = $fax; }
    
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    
    public function getContactPerson() { return $this->contactPerson; }
    public function setContactPerson($contactPerson) { $this->contactPerson = $contactPerson; }
    
    public function getContactPersonMobilePhone() { return $this->contactPersonMobilePhone; }
    public function setContactPersonMobilePhone($contactPersonMobilePhone) { $this->contactPersonMobilePhone = $contactPersonMobilePhone; }
    
    public function getDirectorName() { return $this->directorName; }
    public function setDirectorName($directorName) { $this->directorName = $directorName; }
    
    public function getDirectorMobilePhone() { return $this->directorMobilePhone; }
    public function setDirectorMobilePhone($directorMobilePhone) { $this->directorMobilePhone = $directorMobilePhone; }
    
    public function getIsBank() { return $this->isBank; }
    public function setIsBank($isBank) { $this->isBank = $isBank; }
    
    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
}
