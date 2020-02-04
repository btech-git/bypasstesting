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
use AppBundle\Entity\Transaction\DeliveryInspectionHeader;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;

class DeliveryInspectionHeaderGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $months = array_flip(array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'));
        
        $builder->searchWidget()
            ->addGroup('deliveryInspectionHeader')
                ->setEntityName(DeliveryInspectionHeader::class)
                ->addField('codeNumber')
                    ->setReferences(array('codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear'))
                    ->addOperator(EqualNonEmptyType::class)
                        ->getInput(1, 1)
                            ->setPlaceHolder('Number')
                            ->setAttributes(array('size' => 5, 'maxlength' => 4))
                        ->getInput(1, 2)
                            ->setListData($months)
                            ->setPlaceHolder('Month')
                        ->getInput(1, 3)
                            ->setPlaceHolder('Year')
                            ->setAttributes(array('size' => 3, 'maxlength' => 2))
                ->addField('transactionDate')
                    ->addOperator(EqualNonEmptyType::class)
                        ->getInput(1)
                            ->setAttributes(array('data-pick' => 'date'))
                ->addField('note')
                    ->addOperator(EqualNonEmptyType::class)
            ->addGroup('purchaseDeliveryOrder')
                ->setEntityName(PurchaseDeliveryOrder::class)
                ->addField('vehicleChassisNumber')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('vehicleMachineNumber')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('deliveryInspectionHeader')
                ->addField('transactionDate')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('isVehicleComplete')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('isBodyBuilderExecuted')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('note')
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
            if ($group === 'purchaseDeliveryOrder' && $field === 'vehicleChassisNumber' && $operator === ContainNonEmptyType::class && $values[0] !== null && $values[0] !== '') {
                $associations['receiveOrder']['associations']['purchaseDeliveryOrder']['merge'] = true;
            }
            if ($group === 'purchaseDeliveryOrder' && $field === 'vehicleMachineNumber' && $operator === ContainNonEmptyType::class && $values[0] !== null && $values[0] !== '') {
                $associations['receiveOrder']['associations']['purchaseDeliveryOrder']['merge'] = true;
            }
            $operator::search($criteria[$group], $field, $values);
        });

        $builder->processSort(function($operator, $field, $group) use ($criteria) {
            $operator::sort($criteria[$group], $field);
        });

        $builder->processPage($repository->count($criteria['deliveryInspectionHeader'], $associations), function($offset, $size) use ($criteria) {
            $criteria['deliveryInspectionHeader']->setMaxResults($size);
            $criteria['deliveryInspectionHeader']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['deliveryInspectionHeader'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('deliveryInspectionHeader', 'purchaseDeliveryOrder');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array(
            'receiveOrder' => array('criteria' => null, 'associations' => array(
                'purchaseDeliveryOrder' => array('criteria' => $criteria['purchaseDeliveryOrder']),
            )),
        );

        return array($criteria, $associations);
    }
}
