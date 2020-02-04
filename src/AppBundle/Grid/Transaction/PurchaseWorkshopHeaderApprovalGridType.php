<?php

namespace AppBundle\Grid\Transaction;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use LibBundle\Grid\DataGridType;
use LibBundle\Grid\WidgetsBuilder;
use LibBundle\Grid\DataBuilder;
use LibBundle\Grid\SortOperator\BlankType as SortBlankType;
use LibBundle\Grid\SortOperator\AscendingType;
use LibBundle\Grid\SortOperator\DescendingType;
use LibBundle\Grid\SearchOperator\EqualNonEmptyType;
use LibBundle\Grid\SearchOperator\ContainNonEmptyType;
use LibBundle\Grid\SearchOperator\BlankType as SearchBlankType;
use LibBundle\Grid\SearchOperator\EqualType;
use LibBundle\Grid\SearchOperator\ContainType;
use AppBundle\Entity\Transaction\PurchaseWorkshopHeader;
use AppBundle\Entity\Master\Supplier;

class PurchaseWorkshopHeaderApprovalGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->pageWidget()
            ->addPageSizeField()
                ->addItems(50, 100)
            ->addPageNumField()
        ;
    }

    public function buildData(DataBuilder $builder, ObjectRepository $repository, array $options)
    {
        list($criteria, $associations) = $this->getSpecifications($options);

        $builder->processPage($repository->count($criteria['purchaseWorkshopHeader'], $associations), function($offset, $size) use ($criteria) {
            $criteria['purchaseWorkshopHeader']->setMaxResults($size);
            $criteria['purchaseWorkshopHeader']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['purchaseWorkshopHeader'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('purchaseWorkshopHeader', 'supplier');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array();
        
        $expr = $criteria['purchaseWorkshopHeader']->expr();
        $criteria['purchaseWorkshopHeader']->andWhere($expr->eq('approveOrRejectStatus', ''));

        return array($criteria, $associations);
    }
}
