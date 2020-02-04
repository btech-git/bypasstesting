<?php

namespace AppBundle\Grid\Transaction;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use LibBundle\Grid\DataGridType;
use LibBundle\Grid\WidgetsBuilder;
use LibBundle\Grid\DataBuilder;

class SaleOrderApprovalGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->pageWidget()
            ->addPageSizeField()
                ->addItems(50, 100)
            ->addPageNumField()
        ;
    }

    public function buildData(DataBuilder $builder, ObjectRepository $repository, array $options)
    {
        list($criteria, $associations) = $this->getSpecifications($options);
        
        $criteria['saleOrder']->orderBy(array('transactionDate' => Criteria::ASC));
        
        $builder->processPage($repository->count($criteria['saleOrder'], $associations), function($offset, $size) use ($criteria) {
            $criteria['saleOrder']->setMaxResults($size);
            $criteria['saleOrder']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['saleOrder'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('saleOrder');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array();

        if (array_key_exists('user', $options)) {
            $expr = $criteria['saleOrder']->expr();
            $user = $options['user'];
            $roles = $user->getRoles();
            $role = $roles[2];
            switch ($role) {
                case 'ROLE_DIRECTOR':
                    $roleApproval = 'ROLE_BRANCH_MANAGER';
                    $total = 1000000000;
                    break;
                case 'ROLE_BRANCH_MANAGER':
                    $roleApproval = 'ROLE_SALES_MANAGER';
                    $total = 500000000;
                    break;
                case 'ROLE_SALES_MANAGER':
                    $roleApproval = '';
                    $total = 0;
                    break;
                default:
                    $roleApproval = null;
                    $total = null;
            }
            $expression = $expr->andX($expr->eq('isApproved', false), $expr->eq('isRejected', false), $expr->eq('roleApproval', $roleApproval), $expr->gt('total', $total));
            $criteria['saleOrder']->andWhere($expression);
        }

        return array($criteria, $associations);
    }
}
