<?php

namespace AppBundle\Form\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Master\Customer;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prefix')
            ->add('name')
            ->add('birthDate', BirthdayType::class)
            ->add('officeAddress')
            ->add('officeCity')
            ->add('officeNeighbourhood')
            ->add('officeVillage')
            ->add('officeSubdistrict')
            ->add('officeDistrict')
            ->add('officeProvince')
            ->add('officeZipCode')
            ->add('warehouseAddress')
            ->add('warehouseCity')
            ->add('warehouseProvince')
            ->add('warehouseZipCode')
            ->add('phone')
            ->add('fax')
            ->add('mobilePhone')
            ->add('contactPerson')
            ->add('position')
            ->add('email')
            ->add('taxNumber')
            ->add('webPage')
            ->add('businessField')
            ->add('creditPaymentTerm')
            ->add('categoryTwoHinoPopulation')
            ->add('categoryTwoMitsubishiPopulation')
            ->add('categoryTwoToyotaPopulation')
            ->add('categoryTwoIsuzuPopulation')
            ->add('categoryTwoOtherPopulation')
            ->add('categoryThreeHinoPopulation')
            ->add('categoryThreeMitsubishiPopulation')
            ->add('categoryThreeNissanPopulation')
            ->add('categoryThreeIsuzuPopulation')
            ->add('categoryThreeBenzPopulation')
            ->add('note')
            ->add('isPersonal')
            ->add('isCompany')
            ->add('isActive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Customer::class,
        ));
    }
}
