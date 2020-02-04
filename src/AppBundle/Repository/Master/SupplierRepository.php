<?php

namespace AppBundle\Repository\Master;

use LibBundle\Doctrine\EntityRepository;

class SupplierRepository extends EntityRepository
{
    public function findMainUnitRecord()
    {
        return $this->find(1);
    }
}