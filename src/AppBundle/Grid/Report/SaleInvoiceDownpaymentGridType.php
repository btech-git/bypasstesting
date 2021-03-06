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
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;

class SaleInvoiceDownpaymentGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {        
        $builder->searchWidget()
            ->addGroup('saleInvoiceDownpayment')
                ->setEntityName(SaleInvoiceDownpayment::class)
                ->addField('transactionDate')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(BetweenType::class)
                        ->getInput(1)
                            ->setAttributes(array('data-pick' => 'date'))
                        ->getInput(2)
                            ->setAttributes(array('data-pick' => 'date'))
        ;

        $builder->sortWidget()
            ->addGroup('saleInvoiceDownpayment')
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

        $builder->processPage($repository->count($criteria['saleInvoiceDownpayment'], $associations), function($offset, $size) use ($criteria) {
            $criteria['saleInvoiceDownpayment']->setMaxResults($size);
            $criteria['saleInvoiceDownpayment']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['saleInvoiceDownpayment'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('saleInvoiceDownpayment', 'saleOrder');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array(
            'saleOrder' => array('criteria' => null),
        );

        return array($criteria, $associations);
    }
}
