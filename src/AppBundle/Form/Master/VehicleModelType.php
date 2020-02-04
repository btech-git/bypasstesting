<?php

namespace AppBundle\Form\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Master\VehicleModel;

class VehicleModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('manufactureCode')
            ->add('vldModelName')
            ->add('vosModelName')
            ->add('dmsVariantName')
            ->add('sundry')
            ->add('description')
            ->add('isActive')
            ->add('vehicleModelType')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => VehicleModel::class,
        ));
    }
}
