<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\ReceiveWorkshop;
use AppBundle\Repository\Transaction\ReceiveWorkshopRepository;

class ReceiveWorkshopForm
{
    private $receiveWorkshopRepository;
    
    public function __construct(ReceiveWorkshopRepository $receiveWorkshopRepository)
    {
        $this->receiveWorkshopRepository = $receiveWorkshopRepository;
    }
    
    public function initialize(ReceiveWorkshop $receiveWorkshop, array $params = array())
    {
        list($staff) = array($params['staff']);
        
        if (empty($receiveWorkshop->getId())) {
            $receiveWorkshop->setStaffFirst($staff);
        }
        $receiveWorkshop->setStaffLast($staff);
    }
    
    public function finalize(ReceiveWorkshop $receiveWorkshop, array $params = array())
    {
        if (empty($receiveWorkshop->getId())) {
            $transactionDate = $receiveWorkshop->getTransactionDate();
            if ($transactionDate !== null) {
                $month = intval($transactionDate->format('m'));
                $year = intval($transactionDate->format('y'));
                $lastReceiveWorkshopApplication = $this->receiveWorkshopRepository->findRecentBy($year, $month);
                $currentReceiveWorkshop = ($lastReceiveWorkshopApplication === null) ? $receiveWorkshop : $lastReceiveWorkshopApplication;
                $receiveWorkshop->setCodeNumberToNext($currentReceiveWorkshop->getCodeNumber(), $year, $month);
            }
        }
        $this->sync($receiveWorkshop);
    }
    
    private function sync(ReceiveWorkshop $receiveWorkshop)
    {
    }
    
    public function save(ReceiveWorkshop $receiveWorkshop)
    {
        if (empty($receiveWorkshop->getId())) {
            ObjectPersister::save(function() use ($receiveWorkshop) {
                $this->receiveWorkshopRepository->add($receiveWorkshop);
            });
        } else {
            ObjectPersister::save(function() use ($receiveWorkshop) {
                $this->receiveWorkshopRepository->update($receiveWorkshop);
            });
        }
    }
    
    public function delete(ReceiveWorkshop $receiveWorkshop)
    {
        $this->beforeDelete($receiveWorkshop);
        if (!empty($receiveWorkshop->getId())) {
            ObjectPersister::save(function() use ($receiveWorkshop) {
                $this->receiveWorkshopRepository->remove($receiveWorkshop);
            });
        }
    }
    
    protected function beforeDelete(ReceiveWorkshop $receiveWorkshop)
    {
        $this->sync($receiveWorkshop);
    }
}
