<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\SalePaymentHeader;
use AppBundle\Entity\Transaction\SalePaymentDetail;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;

class SalePaymentHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('note')
            ->add('saleInvoiceHeader', EntityTextType::class, array('class' => SaleInvoiceHeader::class))
            ->add('saleInvoiceDownpayment', EntityTextType::class, array('class' => SaleInvoiceDownpayment::class))
            ->add('salePaymentDetails', CollectionType::class, array(
                'entry_type' => SalePaymentDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SalePaymentDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $salePaymentHeader = $event->getData();
                $options['service']->initialize($salePaymentHeader, $options['init']);
                $form = $event->getForm();
                $formOptions = array(
                    'mapped' => false,
                    'expanded' => true,
                    'choices' => array('Invoice Downpayment' => false, 'Invoice Unit' => true),
                    'choices_as_values' => true,
                );
                if (!empty($salePaymentHeader->getId())) {
                    $formOptions['disabled'] = true;
                    if ($salePaymentHeader->getSaleInvoiceHeader() !== null && $salePaymentHeader->getSaleInvoiceDownpayment() === null) {
                        $formOptions['data'] = true;
                    } else if ($salePaymentHeader->getSaleInvoiceDownpayment() !== null && $salePaymentHeader->getSaleInvoiceHeader() === null) {
                        $formOptions['data'] = false;
                    }
                }
                $form->add('isInvoiceUnit', ChoiceType::class, $formOptions);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $salePaymentHeader = $event->getData();
                $options['service']->finalize($salePaymentHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalePaymentHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
