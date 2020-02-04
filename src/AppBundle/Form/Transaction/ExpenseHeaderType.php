<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use LibBundle\Doctrine\EntityRepository;
use LibBundle\Util\ConstantValueList;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Master\Account;
use AppBundle\Entity\Transaction\ExpenseDetail;
use AppBundle\Entity\Transaction\ExpenseHeader;
use AppBundle\Entity\Transaction\PurchaseDeliveryOrder;

class ExpenseHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('objectiveReason')
            ->add('chequeNumber')
            ->add('note')
            ->add('transactionType', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(ExpenseHeader::class, 'TRANSACTION_TYPE'),
                'choices_as_values' => true,
            ))
            ->add('account', EntityType::class, array(
                'class' => Account::class,
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('u');
                    return $qb->where($qb->expr()->in('IDENTITY(u.accountCategory)', array(10)));
                },
            ))
            ->add('purchaseDeliveryOrder', EntityTextType::class, array('class' => PurchaseDeliveryOrder::class))
            ->add('expenseDetails', CollectionType::class, array(
                'entry_type' => ExpenseDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new ExpenseDetail(),
                'label' => false,
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $expenseHeader = $event->getData();
                $options['service']->initialize($expenseHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $expenseHeader = $event->getData();
                $options['service']->finalize($expenseHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ExpenseHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
