<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\SaleInvoiceDetailUnitDownpayment;
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;
use LibBundle\Form\Type\EntityHiddenType;

class SaleInvoiceDetailUnitDownpaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('saleInvoiceDownpayment', EntityHiddenType::class, array('class' => SaleInvoiceDownpayment::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleInvoiceDetailUnitDownpayment::class,
        ));
    }
}
