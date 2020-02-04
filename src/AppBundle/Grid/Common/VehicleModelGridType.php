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
use AppBundle\Entity\Master\VehicleModel;

class VehicleModelGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->searchWidget()
            ->addGroup('vehicleModel')
                ->setEntityName(VehicleModel::class)
                ->addField('manufactureCode')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('vosModelName')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('dmsVariantName')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('vehicleModel')
                ->addField('manufactureCode')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('vosModelName')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('dmsVariantName')
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

        $builder->processPage($repository->count($criteria['vehicleModel'], $associations), function($offset, $size) use ($criteria) {
            $criteria['vehicleModel']->setMaxResults($size);
            $criteria['vehicleModel']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['vehicleModel'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('vehicleModel');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array();

        if (array_key_exists('form', $options)) {
//            $expr = Criteria::expr();
            switch ($options['form']) {
//                case 'purchase_delivery_order':
//                    $associations['purchaseDeliveryOrders']['merge'] = true;
//                    break;
//                case 'sale_discount_application':
//                    $associations['saleDiscountApplications']['merge'] = true;
//                    break;
//                case 'sale_invoice_detail_unit':
//                    $associations['saleInvoiceDetailUnits']['merge'] = true;
//                    break;
//                case 'sale_order':
//                    $associations['saleOrders']['merge'] = true;
//                    break;
            }
        }

        return array($criteria, $associations);
    }
}
