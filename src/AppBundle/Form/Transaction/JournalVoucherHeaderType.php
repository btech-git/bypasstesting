<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use LibBundle\Util\ConstantValueList;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\JournalVoucherDetail;
use AppBundle\Entity\Transaction\JournalVoucherHeader;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;

class JournalVoucherHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('transactionType', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(JournalVoucherHeader::class, 'TRANSACTION_TYPE'),
                'choices_as_values' => true,
            ))
            ->add('purchaseDeliveryOrder', EntityTextType::class, array('class' => PurchaseDeliveryOrder::class))
            ->add('journalVoucherDetails', CollectionType::class, array(
                'entry_type' => JournalVoucherDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new JournalVoucherDetail(),
                'label' => false,
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $journalVoucherHeader = $event->getData();
                $options['service']->initialize($journalVoucherHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $journalVoucherHeader = $event->getData();
                $options['service']->finalize($journalVoucherHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => JournalVoucherHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
