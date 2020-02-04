<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\SaleOrder;
use AppBundle\Entity\Master\Customer;
use AppBundle\Entity\Master\VehicleModel;

class SaleOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codeNumberOrdinal')
            ->add('transactionDate', 'date')
            ->add('quotationNumber')
            ->add('purchaseOrderDate', 'date')
            ->add('purchaseOrderNumber')
            ->add('deliveryDate', 'date')
            ->add('invoiceRegistrationName')
            ->add('isOffTheRoad')
            ->add('vehicleBrand')
            ->add('vehicleSerialNumber')
            ->add('vehicleColor')
            ->add('isWorkshopNeeded')
            ->add('vehicleOptionalInfo', TextareaType::class)
            ->add('vehicleAccessoriesInfo', TextareaType::class)
            ->add('vehicleOtherInfo', TextareaType::class)
            ->add('quantity')
            ->add('unitPrice')
            ->add('isCash')
            ->add('isLeasing')
            ->add('leasingTerm')
            ->add('leasingMonthlyNominal')
            ->add('downPayment')
            ->add('deliveryAddress', TextareaType::class)
            ->add('note')
            ->add('financeCompany')
            ->add('customer', EntityTextType::class, array('class' => Customer::class))
            ->add('vehicleModel', EntityTextType::class, array('class' => VehicleModel::class))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleOrder = $event->getData();
                $options['service']->initialize($saleOrder, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleOrder = $event->getData();
                $options['service']->finalize($saleOrder);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleOrder::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
