<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Master\VehicleModel;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;
use AppBundle\Entity\Transaction\SaleOrder;

class PurchaseDeliveryOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('reference')
            ->add('vehicleProductionYear', null, array('label' => false))
            ->add('vehicleChassisNumber', null, array('label' => false))
            ->add('vehicleMachineNumber', null, array('label' => false))
            ->add('vehicleDescription', null, array('label' => false))
            ->add('note')
            ->add('isStock', ChoiceType::class, array(
                'expanded' => true,
                'choices' => array('Non-stok' => false, 'Stok' => true),
                'choices_as_values' => true,
            ))  
            ->add('vehicleModel', EntityTextType::class, array('class' => VehicleModel::class))
            ->add('saleOrder', EntityTextType::class, array('class' => SaleOrder::class, 'constraints' => array(
                new Callback(function($object, ExecutionContextInterface $context) {
                    $purchaseDeliveryOrder = $context->getRoot()->getData();
                    if (!$purchaseDeliveryOrder->getIsStock()) {
                        $violations = $context->getValidator()->inContext($context)->validate($object, new NotNull());
                        foreach ($violations as $violation) {
                            $context->getViolations()->add($violation);
                        }
                    }
                }),
            )))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchaseDeliveryOrder = $event->getData();
                $options['service']->initialize($purchaseDeliveryOrder, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $purchaseDeliveryOrder = $event->getData();
                $options['service']->finalize($purchaseDeliveryOrder);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseDeliveryOrder::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
