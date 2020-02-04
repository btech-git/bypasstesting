<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\Count;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Transaction\PurchaseInvoiceDetailUnit;
use AppBundle\Entity\Master\Supplier;

class PurchaseInvoiceHeaderUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('supplierInvoiceNumber')
            ->add('taxInvoiceDate', 'date')
            ->add('taxInvoiceNumber')
            ->add('taxNominal', HiddenType::class)
            ->add('taxNominalReplacement')
            ->add('note')
            ->add('isTax')
            ->add('supplier', EntityTextType::class, array('class' => Supplier::class))
            ->add('purchaseInvoiceDetailUnits', CollectionType::class, array(
                'entry_type' => PurchaseInvoiceDetailUnitType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseInvoiceDetailUnit(),
                'label' => false,
                'constraints' => array(
                    new Valid(),
                    new Count(array('min' => 1)),
                ),
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchaseInvoiceHeader = $event->getData();
                $options['service']->initialize($purchaseInvoiceHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $purchaseInvoiceHeader = $event->getData();
                $options['service']->finalize($purchaseInvoiceHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseInvoiceHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
