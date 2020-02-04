<?php

namespace AppBundle\Grid\Common;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use LibBundle\Grid\DataGridType;
use LibBundle\Grid\WidgetsBuilder;
use LibBundle\Grid\DataBuilder;
use LibBundle\Grid\SortOperator\BlankType as SortBlankType;
use LibBundle\Grid\SortOperator\AscendingType;
use LibBundle\Grid\SortOperator\DescendingType;
use LibBundle\Grid\SearchOperator\ContainNonEmptyType;
use AppBundle\Entity\Master\Supplier;

class SupplierGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->searchWidget()
            ->addGroup('supplier')
                ->setEntityName(Supplier::class)
                ->addField('name')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('contactPerson')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('phone')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('supplier')
                ->addField('name')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('contactPerson')
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

        $builder->processPage($repository->count($criteria['supplier'], $associations), function($offset, $size) use ($criteria) {
            $criteria['supplier']->setMaxResults($size);
            $criteria['supplier']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['supplier'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('supplier');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array();

        if (array_key_exists('form', $options)) {
            $expr = Criteria::expr();
            switch ($options['form']) {
                case 'purchase_invoice_header_general':
                    $criteria['supplier']->andWhere($expr->eq('businessType', Supplier::BUSINESS_TYPE_GENERAL));
                    break;
                case 'purchase_invoice_header_unit':
                    $criteria['supplier']->andWhere($expr->eq('businessType', Supplier::BUSINESS_TYPE_UNIT));
                    break;
                case 'purchase_invoice_header_workshop':
                    $criteria['supplier']->andWhere($expr->eq('businessType', Supplier::BUSINESS_TYPE_WORKSHOP));
                    break;
                case 'purchase_workshop_header':
                    $criteria['supplier']->andWhere($expr->eq('businessType', Supplier::BUSINESS_TYPE_WORKSHOP));
                    break;
                case 'sale_discount_application':
                    $criteria['supplier']->andWhere($expr->eq('businessType', Supplier::BUSINESS_TYPE_WORKSHOP));
                    break;
            }
        }

        return array($criteria, $associations);
    }
}
