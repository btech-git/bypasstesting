<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_inspection_item_category") @ORM\Entity
 * @UniqueEntity("name")
 */
class InspectionItemCategory
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isActive = true;
    /**
     * @ORM\OneToMany(targetEntity="InspectionItem", mappedBy="inspectionItemCategory")
     */
    private $inspectionItems;
    
    public function __construct()
    {
        $this->inspectionItems = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name;
    }
    
    public function getId() { return $this->id; }
    
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
    
    public function getInspectionItems() { return $this->inspectionItems; }
    public function setInspectionItems(Collection $inspectionItems) { $this->inspectionItems = $inspectionItems; }
}
