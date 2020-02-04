<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\NotNull;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Transaction\ReceiveWorkshop;
use AppBundle\Entity\Transaction\PurchaseInvoiceDetailWorkshop;

class PurchaseInvoiceHeaderWorkshopType extends AbstractType
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
            ->add('purchaseInvoiceDetailWorkshops', CollectionType::class, array(
                'entry_type' => PurchaseInvoiceDetailWorkshopType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseInvoiceDetailWorkshop(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchaseInvoiceHeader = $event->getData();
                $options['service']->initialize($purchaseInvoiceHeader, $options['init']);
                $form = $event->getForm();
                $formOptions = array(
                    'class' => ReceiveWorkshop::class,
                    'constraints' => array(new NotNull()),
                );
                if (!empty($purchaseInvoiceHeader->getId())) {
                    $formOptions['disabled'] = true;
                }
                $form->add('receiveWorkshop', EntityTextType::class, $formOptions);
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
