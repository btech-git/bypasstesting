<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\ReceiveOrder;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;

class ReceiveOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('deliveryDate', 'date')
            ->add('serviceBookNumber')
            ->add('note')
            ->add('purchaseDeliveryOrder', EntityTextType::class, array('class' => PurchaseDeliveryOrder::class))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $receiveOrder = $event->getData();
                $options['service']->initialize($receiveOrder, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $receiveOrder = $event->getData();
                $options['service']->finalize($receiveOrder);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ReceiveOrder::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
