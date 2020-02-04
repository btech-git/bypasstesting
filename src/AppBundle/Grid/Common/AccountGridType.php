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
use LibBundle\Grid\SearchOperator\BlankType as SearchBlankType;
use LibBundle\Grid\SearchOperator\EqualNonEmptyType;
use LibBundle\Grid\SearchOperator\ContainNonEmptyType;
use LibBundle\Grid\Transformer\EntityTransformer;
use AppBundle\Entity\Master\AccountCategory;
use AppBundle\Entity\Master\Account;

class AccountGridType extends DataGridType
{
    /**
     * @param WidgetsBuilder $builder
     * @param array $options
     */
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $em = $options['em'];
        $accountCategories = $em->getRepository(AccountCategory::class)->findAll();
        $accountCategoryLabelModifier = function($accountCategory) { return $accountCategory->getName(); };

        $builder->searchWidget()
            ->addGroup('account')
                ->setEntityName(Account::class)
                ->addField('code')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('name')
                    ->addOperator(ContainNonEmptyType::class)
            ->addGroup('accountCategory')
                ->setEntityName(AccountCategory::class)
                ->addField('id')
                    ->setDataTransformer(new EntityTransformer($em, AccountCategory::class))
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualNonEmptyType::class)
                        ->getInput(1)
                            ->setListData($accountCategories, $accountCategoryLabelModifier, null)
        ;

        $builder->sortWidget()
            ->addGroup('account')
                ->addField('code')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('name')
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
            if ($group === 'accountCategory' && $field === 'id' && $operator === EqualNonEmptyType::class && $values[0] !== null && $values[0] !== '') {
                $associations['accountCategory']['merge'] = true;
            }
            $operator::search($criteria[$group], $field, $values);
        });

        $builder->processSort(function($operator, $field, $group) use ($criteria) {
            $operator::sort($criteria[$group], $field);
        });

        $builder->processPage($repository->count($criteria['account'], $associations), function($offset, $size) use ($criteria) {
            $criteria['account']->setMaxResults($size);
            $criteria['account']->setFirstResult($offset);
        });

        $objects = $repository->match($criteria['account'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('account', 'accountCategory');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array(
            'accountCategory' => array('criteria' => $criteria['accountCategory']),
        );

        return array($criteria, $associations);
    }
}
