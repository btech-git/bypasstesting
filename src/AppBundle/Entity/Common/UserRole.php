<?php

namespace AppBundle\Entity\Common;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="common_user_role")
 * @ORM\Entity
 */
class UserRole implements RoleInterface
{
    /**
     * @ORM\Column(type="integer") @ORM\Id() @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $level;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $ordinal;
    /**
     * @ORM\ManyToOne(targetEntity="UserRole") @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $parent;
    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="userRoles")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name;
    }

    public function getId() { return $this->id; }
    
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getLevel() { return $this->level; }
    public function setLevel($level) { $this->level = $level; }
    
    public function getOrdinal() { return $this->ordinal; }
    public function setOrdinal($ordinal) { $this->ordinal = $ordinal; }
    
    public function getParent() { return $this->parent; }
    public function setParent(UserRole $parent = null) { $this->parent = $parent; }
    
    public function getUsers() { return $this->users; }
    public function setUsers(Collection $users) { $this->users = $users; }
    
    public function getRole()
    {
        return str_replace(' ', '_', 'ROLE_' . strtoupper($this->name));
    }
}
