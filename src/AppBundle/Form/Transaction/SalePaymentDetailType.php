<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\SalePaymentDetail;
use LibBundle\Form\Type\EntityHiddenType;

class SalePaymentDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount')
            ->add('memo')
            ->add('paymentMethod')
            ->add('account', EntityHiddenType::class, array('class' => 'AppBundle\Entity\Master\Account'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalePaymentDetail::class,
        ));
    }
}
