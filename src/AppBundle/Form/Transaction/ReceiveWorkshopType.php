<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\ReceiveWorkshop;
use AppBundle\Entity\Transaction\DeliveryWorkshop;

class ReceiveWorkshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('supplierDeliveryNumber')
            ->add('note')
            ->add('deliveryWorkshop', EntityTextType::class, array('class' => DeliveryWorkshop::class))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $receiveWorkshop = $event->getData();
                $options['service']->initialize($receiveWorkshop, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $receiveWorkshop = $event->getData();
                $options['service']->finalize($receiveWorkshop);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ReceiveWorkshop::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
