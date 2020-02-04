<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\PurchaseWorkshopHeader;
use AppBundle\Entity\Transaction\PurchaseWorkshopDetail;
use AppBundle\Entity\Transaction\SaleOrder;
use AppBundle\Entity\Master\Supplier;

class PurchaseWorkshopHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('note')
            ->add('isTax')
            ->add('taxNominal', HiddenType::class)
            ->add('taxNominalReplacement')
            ->add('supplier', EntityTextType::class, array('class' => Supplier::class))
            ->add('saleOrder', EntityTextType::class, array('class' => SaleOrder::class))
            ->add('purchaseWorkshopDetails', CollectionType::class, array(
                'entry_type' => PurchaseWorkshopDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseWorkshopDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchaseWorkshopHeader = $event->getData();
                $options['service']->initialize($purchaseWorkshopHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $purchaseWorkshopHeader = $event->getData();
                $options['service']->finalize($purchaseWorkshopHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseWorkshopHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
