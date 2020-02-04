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
use AppBundle\Entity\Transaction\SaleInvoiceSparepartHeader;

class SaleInvoiceSparepartHeaderGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->searchWidget()
            ->addGroup('saleInvoiceSparepartHeader')
                ->setEntityName(SaleInvoiceSparepartHeader::class)
                ->addField('invoiceNumber')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('transactionDate')
                    ->addOperator(EqualNonEmptyType::class)
                        ->getInput(1)
                            ->setAttributes(array('data-pick' => 'date'))
                ->addField('workOrderNumber')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('customerName')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('taxNumber')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('grandTotalAfterTax')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('saleInvoiceSparepartHeader')
                ->addField('transactionDate')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('invoiceNumber')
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

        $builder->processPage($repository->count($criteria['saleInvoiceSparepartHeader'], $associations), function($offset, $size) use ($criteria) {
            $criteria['saleInvoiceSparepartHeader']->setMaxResults($size);
            $criteria['saleInvoiceSparepartHeader']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['saleInvoiceSparepartHeader'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('saleInvoiceSparepartHeader');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array();

        return array($criteria, $associations);
    }
}
