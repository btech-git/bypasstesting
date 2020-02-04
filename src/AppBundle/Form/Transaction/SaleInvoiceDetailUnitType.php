<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\SaleInvoiceDetailUnit;
use AppBundle\Entity\Transaction\ReceiveOrder;
use LibBundle\Form\Type\EntityHiddenType;

class SaleInvoiceDetailUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('receiveOrder', EntityHiddenType::class, array('class' => ReceiveOrder::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleInvoiceDetailUnit::class,
        ));
    }
}
