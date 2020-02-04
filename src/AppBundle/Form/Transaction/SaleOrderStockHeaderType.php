<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Transaction\SaleOrder;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;

class SaleOrderStockHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('remaining', HiddenType::class, array('disabled' => true, 'constraints' => array(new GreaterThanOrEqual(0))))
            ->add('details', EntityType::class, array(
                'mapped' => false,
                'label' => false,
                'class' => PurchaseDeliveryOrder::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'codeNumber',
                'choices' => $options['purchaseDeliveryOrderRepository']->findBy(array('saleOrder' => null, 'vehicleModel' => $options['vehicleModel'])),
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleOrder = $event->getData();
                $options['service']->initialize($saleOrder);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleOrder = $event->getData();
                $form = $event->getForm();
                $purchaseDeliveryOrders = $saleOrder->getPurchaseDeliveryOrders();
                foreach ($form->get('details')->getData() as $purchaseDeliveryOrder) {
                    if (!$purchaseDeliveryOrders->contains($purchaseDeliveryOrder)) {
                        $purchaseDeliveryOrders->add($purchaseDeliveryOrder);
                    }
                }
                $options['service']->finalize($saleOrder);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleOrder::class,
        ));
        $resolver->setRequired(array('service', 'purchaseDeliveryOrderRepository', 'vehicleModel'));
    }
}
