<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_vehicle_model_type") @ORM\Entity
 * @UniqueEntity("name")
 */
class VehicleModelType
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isActive = true;
    /**
     * @ORM\OneToMany(targetEntity="VehicleModel", mappedBy="vehicleModelType")
     */
    private $vehicleModels;
    
    public function __construct()
    {
        $this->vehicleModels = new ArrayCollection();
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

    public function getVehicleModels() { return $this->vehicleModels; }
    public function setVehicleModels(Collection $vehicleModels) { $this->vehicleModels = $vehicleModels; }
}
