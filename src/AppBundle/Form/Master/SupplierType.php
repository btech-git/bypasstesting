<?php

namespace AppBundle\Form\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use LibBundle\Util\ConstantValueList;
use AppBundle\Entity\Master\Supplier;

class SupplierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('officeAddress')
            ->add('officeCity')
            ->add('officeProvince')
            ->add('officeZipCode')
            ->add('phone')
            ->add('fax')
            ->add('mobilePhone')
            ->add('contactPerson')
            ->add('position')
            ->add('email')
            ->add('taxNumber')
            ->add('webPage')
            ->add('businessField')
            ->add('businessType', ChoiceType::class, array(
                'choices' => ConstantValueList::get(Supplier::class, 'BUSINESS_TYPE'),
                'choices_as_values' => true,
            ))
            ->add('creditPaymentTerm')
            ->add('note')
            ->add('isPersonal')
            ->add('isCompany')
            ->add('isActive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Supplier::class,
        ));
    }
}
