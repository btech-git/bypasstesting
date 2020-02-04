<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\PurchaseInvoiceDetailUnit;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;
use LibBundle\Form\Type\EntityHiddenType;

class PurchaseInvoiceDetailUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unitPrice')
            ->add('discount')
            ->add('purchaseDeliveryOrder', EntityHiddenType::class, array('class' => PurchaseDeliveryOrder::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseInvoiceDetailUnit::class,
        ));
    }
}
