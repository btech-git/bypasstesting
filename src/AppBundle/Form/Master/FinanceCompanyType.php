<?php

namespace AppBundle\Form\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Master\FinanceCompany;

class FinanceCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('branchName')
            ->add('address')
            ->add('phone')
            ->add('fax')
            ->add('email')
            ->add('contactPerson')
            ->add('contactPersonMobilePhone')
            ->add('directorName')
            ->add('directorMobilePhone')
            ->add('isBank')
            ->add('isActive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FinanceCompany::class,
        ));
    }
}
