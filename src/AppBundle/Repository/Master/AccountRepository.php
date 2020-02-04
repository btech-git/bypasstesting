<?php

namespace AppBundle\Repository\Master;

use LibBundle\Doctrine\EntityRepository;

class AccountRepository extends EntityRepository
{
    public function findReceivableRecord()
    {
        return $this->find(7);
    }
    
    public function findInventoryUnitRecord()
    {
        return $this->find(10);
    }
    
    public function findInventoryWorkshopRecord()
    {
        return $this->find(11);
    }
    
    public function findInventorySparepartRecord()
    {
        return $this->find(12);
    }
    
    public function findPayableUnitRecord()
    {
        return $this->find(27);
    }
    
    public function findPayableOtherRecord()
    {
        return $this->find(28);
    }
    
    public function findPayableSparepartRecord()
    {
        return $this->find(29);
    }
    
    public function findSaleUnitRecord()
    {
        return $this->find(50);
    }
    
}