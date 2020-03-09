<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Master\AccountRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("code")
 */
class Account
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank()
     */
    private $code;
    /**
     * @ORM\Column(type="string", length=20)
     */
    private $alias;
    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isCashOrBank = false;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isProfitLoss = false;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isActive = true;
    /**
     * @ORM\ManyToOne(targetEntity="AccountCategory", inversedBy="accounts")
     * @Assert\NotNull()
     */
    private $accountCategory;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Report\JournalLedger", mappedBy="account")
     */
    private $journalLedgers;
    
    public function __construct()
    {
        $this->journalLedgers = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name;
    }
    
    public function getId() { return $this->id; }
    
    public function getCode() { return $this->code; }
    public function setCode($code) { $this->code = $code; }

    public function getAlias() { return $this->alias; }
    public function setAlias($alias) { $this->alias = $alias; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getIsCashOrBank() { return $this->isCashOrBank; }
    public function setIsCashOrBank($isCashOrBank) { $this->isCashOrBank = $isCashOrBank; }

    public function getIsProfitLoss() { return $this->isProfitLoss; }
    public function setIsProfitLoss($isProfitLoss) { $this->isProfitLoss = $isProfitLoss; }

    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    public function getAccountCategory() { return $this->accountCategory; }
    public function setAccountCategory(AccountCategory $accountCategory) { $this->accountCategory = $accountCategory; }
    
    public function getJournalLedgers() { return $this->journalLedgers; }
    public function setJournalLedgers(Collection $journalLedgers) { $this->journalLedgers = $journalLedgers; }
}
