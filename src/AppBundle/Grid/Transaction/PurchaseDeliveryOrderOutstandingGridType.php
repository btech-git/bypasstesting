<?php

namespace AppBundle\Grid\Transaction;

use Doctrine\Common\Persistence\ObjectRepository;
use LibBundle\Grid\DataGridType;
use LibBundle\Grid\WidgetsBuilder;
use LibBundle\Grid\DataBuilder;

class PurchaseDeliveryOrderOutstandingGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->pageWidget()
            ->addPageSizeField()
                ->addItems(10, 25, 50, 100)
            ->addPageNumField()
        ;
    }

    public function buildData(DataBuilder $builder, ObjectRepository $repository, array $options)
    {
        $pageSize = 10;
        $pageOffset = 0;
        
        $builder->processPage($repository->countOutstandings(), function($offset, $size) use (&$pageSize, &$pageOffset) {
            $pageSize = $size;
            $pageOffset = $offset;
        });
        
        $objects = $repository->findOutstandingsBy($pageSize, $pageOffset);

        $builder->setData($objects);
    }
}
