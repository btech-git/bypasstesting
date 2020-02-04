<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\DeliveryOrder;
use AppBundle\Entity\Transaction\DeliveryInspectionHeader;

class DeliveryOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('destinationAddress')
            ->add('destinationPhone')
            ->add('isNetworkBook')
            ->add('isSpareTire')
            ->add('isServiceBook')
            ->add('isOwnerManual')
            ->add('isJackHandle')
            ->add('isLighter')
            ->add('isToolSet')
            ->add('isFourHubcap')
            ->add('isPaintCan')
            ->add('isCarpetKit')
            ->add('isSafetyTriangle')
            ->add('isTwoVehicleKey')
            ->add('note')
            ->add('deliveryInspectionHeader', EntityTextType::class, array('class' => DeliveryInspectionHeader::class))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $deliveryOrder = $event->getData();
                $options['service']->initialize($deliveryOrder, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $deliveryOrder = $event->getData();
                $options['service']->finalize($deliveryOrder);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DeliveryOrder::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
