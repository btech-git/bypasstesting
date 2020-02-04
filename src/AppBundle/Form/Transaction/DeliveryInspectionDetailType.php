<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use LibBundle\Form\Type\EntityHiddenType;
use AppBundle\Entity\Transaction\DeliveryInspectionDetail;
use AppBundle\Entity\Master\InspectionItem;

class DeliveryInspectionDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inspectionItem', EntityHiddenType::class, array('class' => InspectionItem::class))
            ->add('isSelected', CheckboxType::class, array(
                'mapped' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DeliveryInspectionDetail::class,
        ));
    }
}
