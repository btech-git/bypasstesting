<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_customer") @ORM\Entity
 * @UniqueEntity("taxNumber")
 * @Assert\Expression("(this.getIsPersonal() and !this.getIsCompany()) or (!this.getIsPersonal() and this.getIsCompany())")
 */
class Customer
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $prefix;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull() @Assert\Date()
     */
    private $birthDate;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $officeAddress;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $officeCity;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $officeProvince;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Length(min=5, max=5)
     */
    private $officeZipCode;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $warehouseAddress;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $warehouseCity;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $warehouseProvince;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Length(min=5, max=5)
     */
    private $warehouseZipCode;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank() @Assert\Length(min=8, max=20)
     */
    private $phone;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $fax;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
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
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotNull() @Assert\Regex("/^\d{2}.\d{3}.\d{3}.\d-\d{3}.\d{3}$/")
     */
    private $taxNumber;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $webPage;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $businessField;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull()
     */
    private $creditPaymentTerm;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryTwoHinoPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryTwoMitsubishiPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryTwoToyotaPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryTwoIsuzuPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryTwoOtherPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryThreeHinoPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryThreeMitsubishiPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryThreeNissanPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryThreeIsuzuPopulation;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $categoryThreeBenzPopulation;
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

    public function getPrefix() { return $this->prefix; }
    public function setPrefix($prefix) { $this->prefix = $prefix; }
    
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getBirthDate() { return $this->birthDate; }
    public function setBirthDate($birthDate) { $this->birthDate = $birthDate; }

    public function getOfficeAddress() { return $this->officeAddress; }
    public function setOfficeAddress($officeAddress) { $this->officeAddress = $officeAddress; }

    public function getOfficeCity() { return $this->officeCity; }
    public function setOfficeCity($officeCity) { $this->officeCity = $officeCity; }

    public function getOfficeProvince() { return $this->officeProvince; }
    public function setOfficeProvince($officeProvince) { $this->officeProvince = $officeProvince; }

    public function getOfficeZipCode() { return $this->officeZipCode; }
    public function setOfficeZipCode($officeZipCode) { $this->officeZipCode = $officeZipCode; }

    public function getWarehouseAddress() { return $this->warehouseAddress; }
    public function setWarehouseAddress($warehouseAddress) { $this->warehouseAddress = $warehouseAddress; }

    public function getWarehouseCity() { return $this->warehouseCity; }
    public function setWarehouseCity($warehouseCity) { $this->warehouseCity = $warehouseCity; }

    public function getWarehouseProvince() { return $this->warehouseProvince; }
    public function setWarehouseProvince($warehouseProvince) { $this->warehouseProvince = $warehouseProvince; }

    public function getWarehouseZipCode() { return $this->warehouseZipCode; }
    public function setWarehouseZipCode($warehouseZipCode) { $this->warehouseZipCode = $warehouseZipCode; }

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

    public function getTaxNumber() { return $this->taxNumber; }
    public function setTaxNumber($taxNumber) { $this->taxNumber = $taxNumber; }

    public function getWebPage() { return $this->webPage; }
    public function setWebPage($webPage) { $this->webPage = $webPage; }

    public function getBusinessField() { return $this->businessField; }
    public function setBusinessField($businessField) { $this->businessField = $businessField; }

    public function getCreditPaymentTerm() { return $this->creditPaymentTerm; }
    public function setCreditPaymentTerm($creditPaymentTerm) { $this->creditPaymentTerm = $creditPaymentTerm; }

    public function getCategoryTwoHinoPopulation() { return $this->categoryTwoHinoPopulation; }
    public function setCategoryTwoHinoPopulation($categoryTwoHinoPopulation) { $this->categoryTwoHinoPopulation = $categoryTwoHinoPopulation; }

    public function getCategoryTwoMitsubishiPopulation() { return $this->categoryTwoMitsubishiPopulation; }
    public function setCategoryTwoMitsubishiPopulation($categoryTwoMitsubishiPopulation) { $this->categoryTwoMitsubishiPopulation = $categoryTwoMitsubishiPopulation; }

    public function getCategoryTwoToyotaPopulation() { return $this->categoryTwoToyotaPopulation; }
    public function setCategoryTwoToyotaPopulation($categoryTwoToyotaPopulation) { $this->categoryTwoToyotaPopulation = $categoryTwoToyotaPopulation; }

    public function getCategoryTwoIsuzuPopulation() { return $this->categoryTwoIsuzuPopulation; }
    public function setCategoryTwoIsuzuPopulation($categoryTwoIsuzuPopulation) { $this->categoryTwoIsuzuPopulation = $categoryTwoIsuzuPopulation; }

    public function getCategoryTwoOtherPopulation() { return $this->categoryTwoOtherPopulation; }
    public function setCategoryTwoOtherPopulation($categoryTwoOtherPopulation) { $this->categoryTwoOtherPopulation = $categoryTwoOtherPopulation; }

    public function getCategoryThreeHinoPopulation() { return $this->categoryThreeHinoPopulation; }
    public function setCategoryThreeHinoPopulation($categoryThreeHinoPopulation) { $this->categoryThreeHinoPopulation = $categoryThreeHinoPopulation; }

    public function getCategoryThreeMitsubishiPopulation() { return $this->categoryThreeMitsubishiPopulation; }
    public function setCategoryThreeMitsubishiPopulation($categoryThreeMitsubishiPopulation) { $this->categoryThreeMitsubishiPopulation = $categoryThreeMitsubishiPopulation; }

    public function getCategoryThreeNissanPopulation() { return $this->categoryThreeNissanPopulation; }
    public function setCategoryThreeNissanPopulation($categoryThreeNissanPopulation) { $this->categoryThreeNissanPopulation = $categoryThreeNissanPopulation; }

    public function getCategoryThreeIsuzuPopulation() { return $this->categoryThreeIsuzuPopulation; }
    public function setCategoryThreeIsuzuPopulation($categoryThreeIsuzuPopulation) { $this->categoryThreeIsuzuPopulation = $categoryThreeIsuzuPopulation; }

    public function getCategoryThreeBenzPopulation() { return $this->categoryThreeBenzPopulation; }
    public function setCategoryThreeBenzPopulation($categoryThreeBenzPopulation) { $this->categoryThreeBenzPopulation = $categoryThreeBenzPopulation; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getIsPersonal() { return $this->isPersonal; }
    public function setIsPersonal($isPersonal) { $this->isPersonal = $isPersonal; }

    public function getIsCompany() { return $this->isCompany; }
    public function setIsCompany($isCompany) { $this->isCompany = $isCompany; }

    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
}
