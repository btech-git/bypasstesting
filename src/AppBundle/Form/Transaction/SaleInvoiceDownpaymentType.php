<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use LibBundle\Doctrine\EntityRepository;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Master\Account;
use AppBundle\Entity\Transaction\SaleInvoiceDownpayment;
use AppBundle\Entity\Transaction\SaleOrder;

class SaleInvoiceDownpaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('taxNumber')
            ->add('note')
            ->add('amount')
            ->add('paymentMethod')
            ->add('account', EntityType::class, array(
                'class' => Account::class,
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('u');
                    return $qb->where($qb->expr()->in('IDENTITY(u.accountCategory)', array(10)));
                },
            ))
            ->add('saleOrder', EntityTextType::class, array('class' => SaleOrder::class))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleDownpayment = $event->getData();
                $options['service']->initialize($saleDownpayment, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleDownpayment = $event->getData();
                $options['service']->finalize($saleDownpayment);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleInvoiceDownpayment::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
