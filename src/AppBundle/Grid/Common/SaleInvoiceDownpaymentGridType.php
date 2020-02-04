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
use LibBundle\Grid\SearchOperator\EqualNonEmptyType;
use LibBundle\Grid\SearchOperator\ContainNonEmptyType;
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;
use AppBundle\Entity\Master\Customer;

class SaleInvoiceDownpaymentGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $months = array_flip(array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'));

        $builder->searchWidget()
            ->addGroup('saleInvoiceDownpayment')
                ->setEntityName(SaleInvoiceDownpayment::class)
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
            ->addGroup('customer')
                ->setEntityName(Customer::class)
                ->addField('name')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('saleInvoiceDownpayment')
                ->addField('codeNumber')
                    ->setReferences(array('codeNumberYear', 'codeNumberMonth', 'codeNumberOrdinal'))
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('transactionDate')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
            ->addGroup('customer')
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
            if ($group === 'customer' && $field === 'name' && $operator === ContainNonEmptyType::class && $values[0] !== null && $values[0] !== '') {
                $associations['customer']['merge'] = true;
            }
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
        $names = array('saleInvoiceDownpayment', 'customer');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array(
            'customer' => array('criteria' => $criteria['customer']),
        );

        if (array_key_exists('form', $options)) {
            $expr = Criteria::expr();
            switch ($options['form']) {
                case 'sale_invoice_header_unit':
                    if (array_key_exists('customer_id', $options['options'])) {
                        $criteria['customer']->andWhere($expr->eq('id', $options['options']['customer_id']));
                    }
                    $associations['customer']['merge'] = true;
                    $associations['saleInvoiceDetailUnitDownpayments']['merge'] = false;
                    break;
                case 'sale_payment_header':
                    $criteria['saleInvoiceDownpayment']->andWhere($expr->gt('remaining', 0));
                    break;
            }
        }

        return array($criteria, $associations);
    }
}
