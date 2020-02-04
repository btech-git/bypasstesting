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
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Master\Supplier;

class PurchaseInvoiceHeaderGeneralGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $months = array_flip(array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'));
        
        $builder->searchWidget()
            ->addGroup('purchaseInvoiceHeader')
                ->setEntityName(PurchaseInvoiceHeader::class)
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
                ->addField('supplierInvoiceNumber')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('grandTotal')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('totalPayment')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('remaining')
                    ->addOperator(ContainNonEmptyType::class)
            ->addGroup('supplier')
                ->setEntityName(Supplier::class)
                ->addField('name')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('purchaseInvoiceHeader')
                ->addField('transactionDate')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('supplierInvoiceNumber')
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
        $expr = Criteria::expr();
        $criteria = Criteria::create();
        
        $criteria->andWhere($expr->eq('businessType', PurchaseInvoiceHeader::BUSINESS_TYPE_GENERAL));

        $builder->processSearch(function($values, $operator, $field) use ($criteria) {
            $operator::search($criteria, $field, $values);
        });

        $builder->processSort(function($operator, $field) use ($criteria) {
            $operator::sort($criteria, $field);
        });

        $builder->processPage($repository->count($criteria), function($offset, $size) use ($criteria) {
            $criteria->setMaxResults($size);
            $criteria->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria);

        $builder->setData($objects);
    }
}
