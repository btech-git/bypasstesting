<?php

namespace AppBundle\Grid\Report;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use LibBundle\Grid\DataGridType;
use LibBundle\Grid\WidgetsBuilder;
use LibBundle\Grid\DataBuilder;
use LibBundle\Grid\SortOperator\BlankType as SortBlankType;
use LibBundle\Grid\SortOperator\AscendingType;
use LibBundle\Grid\SortOperator\DescendingType;
use LibBundle\Grid\SearchOperator\BlankType as SearchBlankType;
use LibBundle\Grid\SearchOperator\BetweenType;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;

class PurchaseInvoiceDetailWorkshopGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {        
        $builder->searchWidget()
            ->addGroup('purchaseInvoiceHeader')
                ->setEntityName(PurchaseInvoiceHeader::class)
                ->addField('transactionDate')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(BetweenType::class)
                        ->getInput(1)
                            ->setAttributes(array('data-pick' => 'date'))
                        ->getInput(2)
                            ->setAttributes(array('data-pick' => 'date'))
        ;

        $builder->sortWidget()
            ->addGroup('purchaseInvoiceHeader')
                ->addField('transactionDate')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
        ;

        $builder->pageWidget()
            ->addPageSizeField()
                ->addItems(10, 25, 50, 100)
            ->addPageNumField()
        ;
    }

    public function buildData(DataBuilder $builder, ObjectRepository $repository, array $options)
    {
        list($criteria, $associations) = $this->getSpecifications($options);

        $builder->processSearch(function($values, $operator, $field, $group) use ($criteria, &$associations) {
            $operator::search($criteria[$group], $field, $values);
        });

        $builder->processSort(function($operator, $field, $group) use ($criteria) {
            $operator::sort($criteria[$group], $field);
        });

        $builder->processPage($repository->count($criteria['purchaseInvoiceDetailWorkshop'], $associations), function($offset, $size) use ($criteria) {
            $criteria['purchaseInvoiceDetailWorkshop']->setMaxResults($size);
            $criteria['purchaseInvoiceDetailWorkshop']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['purchaseInvoiceDetailWorkshop'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('purchaseInvoiceDetailWorkshop', 'purchaseInvoiceHeader');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array(
            'purchaseInvoiceHeader' => array('criteria' => $criteria['purchaseInvoiceHeader'], 'merge' => true),
        );

        return array($criteria, $associations);
    }
}
