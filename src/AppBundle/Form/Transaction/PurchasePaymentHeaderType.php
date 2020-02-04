<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\PurchasePaymentHeader;
use AppBundle\Entity\Transaction\PurchasePaymentDetail;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;

class PurchasePaymentHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('note')
            ->add('purchaseInvoiceHeader', EntityTextType::class, array('class' => PurchaseInvoiceHeader::class))
            ->add('purchasePaymentDetails', CollectionType::class, array(
                'entry_type' => PurchasePaymentDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchasePaymentDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchasePaymentHeader = $event->getData();
                $options['service']->initialize($purchasePaymentHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $purchasePaymentHeader = $event->getData();
                $options['service']->finalize($purchasePaymentHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchasePaymentHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
