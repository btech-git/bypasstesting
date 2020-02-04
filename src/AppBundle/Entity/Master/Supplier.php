<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_supplier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Master\SupplierRepository")
 * @UniqueEntity("taxNumber")
 * @Assert\Expression("(this.getIsPersonal() and !this.getIsCompany()) or (!this.getIsPersonal() and this.getIsCompany())")
 */
class Supplier
{
    const BUSINESS_TYPE_UNIT = 'hino motor';
    const BUSINESS_TYPE_WORKSHOP = 'karoseri';
    const BUSINESS_TYPE_GENERAL = 'umum';
    
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $officeAddress;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     */
    private $officeCity;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     */
    private $officeProvince;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank() @Assert\Length(min=5, max=5)
     */
    private $officeZipCode;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Length(min=8, max=20)
     */
    private $phone;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $fax;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $mobilePhone;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $contactPerson;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $position;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull() @Assert\Email()
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $businessField;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $businessType;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull()
     */
    private $creditPaymentTerm;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Regex("/^\d{2}.\d{3}.\d{3}.\d-\d{3}.\d{3}$/")
     */
    private $taxNumber;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $webPage;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isPersonal;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isCompany;
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

    public function getOfficeAddress() { return $this->officeAddress; }
    public function setOfficeAddress($officeAddress) { $this->officeAddress = $officeAddress; }

    public function getOfficeCity() { return $this->officeCity; }
    public function setOfficeCity($officeCity) { $this->officeCity = $officeCity; }

    public function getOfficeProvince() { return $this->officeProvince; }
    public function setOfficeProvince($officeProvince) { $this->officeProvince = $officeProvince; }

    public function getOfficeZipCode() { return $this->officeZipCode; }
    public function setOfficeZipCode($officeZipCode) { $this->officeZipCode = $officeZipCode; }

    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }

    public function getFax() { return $this->fax; }
    public function setFax($fax) { $this->fax = $fax; }

    public function getMobilePhone() { return $this->mobilePhone; }
    public function setMobilePhone($mobilePhone) { $this->mobilePhone = $mobilePhone; }

    public function getContactPerson() { return $this->contactPerson; }
    public function setContactPerson($contactPerson) { $this->contactPerson = $contactPerson; }

    public function getPosition() { return $this->position; }
    public function setPosition($position) { $this->position = $position; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getBusinessField() { return $this->businessField; }
    public function setBusinessField($businessField) { $this->businessField = $businessField; }

    public function getBusinessType() { return $this->businessType; }
    public function setBusinessType($businessType) { $this->businessType = $businessType; }

    public function getCreditPaymentTerm() { return $this->creditPaymentTerm; }
    public function setCreditPaymentTerm($creditPaymentTerm) { $this->creditPaymentTerm = $creditPaymentTerm; }

    public function getTaxNumber() { return $this->taxNumber; }
    public function setTaxNumber($taxNumber) { $this->taxNumber = $taxNumber; }

    public function getWebPage() { return $this->webPage; }
    public function setWebPage($webPage) { $this->webPage = $webPage; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getIsPersonal() { return $this->isPersonal; }
    public function setIsPersonal($isPersonal) { $this->isPersonal = $isPersonal; }

    public function getIsCompany() { return $this->isCompany; }
    public function setIsCompany($isCompany) { $this->isCompany = $isCompany; }

    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
}
