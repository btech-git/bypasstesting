<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use LibBundle\Form\Type\EntityTextType;
use LibBundle\Util\ConstantValueList;
use AppBundle\Entity\Transaction\SaleDiscountApplication;
use AppBundle\Entity\Master\Customer;
use AppBundle\Entity\Master\VehicleModel;
use AppBundle\Entity\Master\Supplier;

class SaleDiscountApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('transactionStatus', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(SaleDiscountApplication::class, 'TRANSACTION_STATUS'),
                'choices_as_values' => true,
            ))
            ->add('ownershipStatus', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(SaleDiscountApplication::class, 'OWNERSHIP_STATUS'),
                'choices_as_values' => true,
            ))
            ->add('ownershipCategory', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(SaleDiscountApplication::class, 'OWNERSHIP_CATEGORY'),
                'choices_as_values' => true,
            ))
            ->add('workshopReference', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(SaleDiscountApplication::class, 'WORKSHOP_REFERENCE'),
                'choices_as_values' => true,
            ))
            ->add('requestQuantity')
            ->add('paymentMethodType', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(SaleDiscountApplication::class, 'PAYMENT_METHOD'),
                'choices_as_values' => true,
            ))
            ->add('financeCompany')
            ->add('bookingFee')
            ->add('downpayment1')
            ->add('downpayment2')
            ->add('totalPayment')
            ->add('leasingPrice')
            ->add('leasingPriceDifference')
            ->add('leasingOverPaid')
            ->add('leasingOverPaidNett')
            ->add('leasingTaxAmount')
            ->add('leasingStatus', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(SaleDiscountApplication::class, 'LEASING_STATUS'),
                'choices_as_values' => true,
            ))
            ->add('deliveryType')
            ->add('bookingFee')
            ->add('otherPricingName1')
            ->add('otherPricingName2')
            ->add('otherPricingName3')
            ->add('otherPricingName4')
            ->add('otherPricingName5')
            ->add('otherPricingAmount1')
            ->add('otherPricingAmount2')
            ->add('otherPricingAmount3')
            ->add('otherPricingAmount4')
            ->add('otherPricingAmount5')
            ->add('mediatorPrice')
            ->add('assemblyYear')
            ->add('vehicleColor')
            ->add('orderArea')
            ->add('workshopType')
            ->add('workshopReference')
            ->add('workshopPrice')
            ->add('workshopProfit')
            ->add('isWorkshopSplitPurchase')
            ->add('isCashBeforeDelivery')
            ->add('isPaymentTerm')
            ->add('termOfPayment')
            ->add('leasingReference', ChoiceType::class, array(
                'expanded' => true,
                'choices' => ConstantValueList::get(SaleDiscountApplication::class, 'LEASING_REFERENCE'),
                'choices_as_values' => true,
            ))
            ->add('unitPrice')
            ->add('otherUnitPrice')
            ->add('mediatorName')
            ->add('mediatorRanking')
            ->add('mediatorPhone')
            ->add('mediatorTaxSelection')
            ->add('note')
            ->add('customer', EntityTextType::class, array('class' => Customer::class))
            ->add('vehicleModel', EntityTextType::class, array('class' => VehicleModel::class))
            ->add('supplier', EntityTextType::class, array('class' => Supplier::class))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleDiscountApplication = $event->getData();
                $options['service']->initialize($saleDiscountApplication, $options['init']);
                $form = $event->getForm();
                $formOptions = array();
                if (empty($saleDiscountApplication->getId())) {
                    $formOptions['disabled'] = true;
                }
                $form->add('approvedPrice', null, $formOptions);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleDiscountApplication = $event->getData();
                $options['service']->finalize($saleDiscountApplication);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleDiscountApplication::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
