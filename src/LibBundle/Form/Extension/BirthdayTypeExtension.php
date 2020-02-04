<?php

namespace LibBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BirthdayTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $year = intval(date('Y'));
        $resolver->setDefaults(array(
            'widget' => 'choice',
            'placeholder' => array('day' => 'Day', 'month' => 'Month', 'year' => 'Year'),
            'years' => range($year, $year - 120),
        ));
    }

    public function getExtendedType()
    {
        return BirthdayType::class;
    }
}
