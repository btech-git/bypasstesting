<?php

namespace AppBundle\Grid\Master;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use LibBundle\Grid\DataGridType;
use LibBundle\Grid\WidgetsBuilder;
use LibBundle\Grid\DataBuilder;
use LibBundle\Grid\SortOperator\BlankType as SortBlankType;
use LibBundle\Grid\SortOperator\AscendingType;
use LibBundle\Grid\SortOperator\DescendingType;
use LibBundle\Grid\SearchOperator\BlankType as SearchBlankType;
use LibBundle\Grid\SearchOperator\EqualType;
use LibBundle\Grid\SearchOperator\ContainType;
use AppBundle\Entity\Master\InspectionItem;

class InspectionItemGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->searchWidget()
            ->addGroup('inspectionItem')
                ->setEntityName(InspectionItem::class)
                ->addField('name')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                    ->addOperator(ContainType::class)
                ->addField('isActive')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                        ->getInput(1)
                            ->setListData(array('Yes' => true, 'No' => false))
        ;

        $builder->sortWidget()
            ->addGroup('inspectionItem')
                ->addField('name')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('isActive')
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

        $builder->processSearch(function($values, $operator, $field) use ($criteria) {
            $operator::search($criteria, $field, $values);
        });

        $builder->processSort(function($operator, $field) use ($criteria) {
            $operator::sort($criteria, $field);
        });

        $builder->processPage($repository->count($criteria['inspectionItem'], $associations), function($offset, $size) use ($criteria) {
            $criteria['inspectionItem']->setMaxResults($size);
            $criteria['inspectionItem']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['inspectionItem'], $associations);

        $builder->setData($objects);
    }
    
    private function getSpecifications(array $options)
    {
        $names = array('inspectionItem');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array();

        if (array_key_exists('form', $options)) {
            switch ($options['form']) {
                
            }
        }

        return array($criteria, $associations);
    }
}
