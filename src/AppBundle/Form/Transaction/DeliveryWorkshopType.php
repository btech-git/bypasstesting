<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\DeliveryWorkshop;
use AppBundle\Entity\Transaction\PurchaseWorkshopHeader;
use AppBundle\Entity\Transaction\ReceiveOrder;

class DeliveryWorkshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('note')
            ->add('purchaseWorkshopHeader', EntityTextType::class, array('class' => PurchaseWorkshopHeader::class))
            ->add('receiveOrder', EntityTextType::class, array('class' => ReceiveOrder::class))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $deliveryWorkshop = $event->getData();
                $options['service']->initialize($deliveryWorkshop, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $deliveryWorkshop = $event->getData();
                $options['service']->finalize($deliveryWorkshop);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DeliveryWorkshop::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
