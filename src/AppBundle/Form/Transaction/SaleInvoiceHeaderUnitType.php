<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\Count;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Entity\Transaction\SaleInvoiceDetailUnit;
use AppBundle\Entity\Transaction\SaleInvoiceDetailUnitDownpayment;
use AppBundle\Entity\Master\Customer;

class SaleInvoiceHeaderUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('taxDate', 'date')
            ->add('taxNumber')
            ->add('note')
            ->add('customer', EntityTextType::class, array('class' => Customer::class))
            ->add('saleInvoiceDetailUnits', CollectionType::class, array(
                'entry_type' => SaleInvoiceDetailUnitType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleInvoiceDetailUnit(),
                'label' => false,
                'constraints' => array(
                    new Valid(),
                    new Count(array('min' => 1, 'max' => 10)),
                ),
            ))
            ->add('saleInvoiceDetailUnitDownpayments', CollectionType::class, array(
                'entry_type' => SaleInvoiceDetailUnitDownpaymentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleInvoiceDetailUnitDownpayment(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleInvoiceHeader = $event->getData();
                $options['service']->initialize($saleInvoiceHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleInvoiceHeader = $event->getData();
                $options['service']->finalize($saleInvoiceHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleInvoiceHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
