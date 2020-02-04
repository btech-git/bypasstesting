<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\DeliveryInspectionHeader;
use AppBundle\Entity\Transaction\DeliveryInspectionDetail;
use AppBundle\Entity\Transaction\ReceiveOrder;

class DeliveryInspectionHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', 'date')
            ->add('isVehicleComplete')
            ->add('isVehicleIncomplete')
            ->add('isBodyBuilderExecuted')
            ->add('note')
            ->add('receiveOrder', EntityTextType::class, array('class' => ReceiveOrder::class))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $deliveryInspectionHeader = $event->getData();
                $options['service']->initialize($deliveryInspectionHeader, $options['init']);
                $form = $event->getForm();
                $formOptions = array(
                    'mapped' => false,
                    'entry_type' => DeliveryInspectionDetailType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype_data' => new DeliveryInspectionDetail(),
                    'label' => false,
                );
                if (empty($deliveryInspectionHeader->getId())) {
                    $formOptions['disabled'] = true;
                }
                $form->add('details', CollectionType::class, $formOptions);
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($options) {
                $deliveryInspectionHeader = $event->getData();
                $form = $event->getForm();
                
                $deliveryInspectionDetails = $deliveryInspectionHeader->getDeliveryInspectionDetails();
                $inspectionItemList = array();
                $deliveryInspectionDetailList = array();
                foreach ($deliveryInspectionDetails as $deliveryInspectionDetail) {
                    $inspectionItemList[] = $deliveryInspectionDetail->getInspectionItem();
                    $deliveryInspectionDetailList[] = $deliveryInspectionDetail;
                }
                $inspectionItems = $options['inspectionItemRepository']->findAll();
                $details = new ArrayCollection();
                foreach ($inspectionItems as $inspectionItem) {
                    if (($key = array_search($inspectionItem, $inspectionItemList)) !== false) {
                        $details->add($deliveryInspectionDetailList[$key]);
                    } else {
                        $detail = new DeliveryInspectionDetail();
                        $detail->setInspectionItem($inspectionItem);
                        $details->add($detail);
                    }
                }
                $form->get('details')->setData($details);
                foreach ($form->get('details') as $item) {
                    if (in_array($item->get('inspectionItem')->getData(), $inspectionItemList)) {
                        $item->get('isSelected')->setData(true);
                    }
                }
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $deliveryInspectionHeader = $event->getData();
                $form = $event->getForm();
                
                $deliveryInspectionDetails = $deliveryInspectionHeader->getDeliveryInspectionDetails();
                $inspectionItemList = array();
                foreach ($deliveryInspectionDetails as $deliveryInspectionDetail) {
                    $inspectionItemList[] = $deliveryInspectionDetail->getInspectionItem();
                }
                foreach ($form->get('details') as $item) {
                    if ($item->get('isSelected')->getData() && !in_array($item->get('inspectionItem')->getData(), $inspectionItemList)) {
                        $deliveryInspectionDetail = new DeliveryInspectionDetail();
                        $deliveryInspectionDetail->setInspectionItem($item->get('inspectionItem')->getData());
                        $deliveryInspectionDetails->add($deliveryInspectionDetail);
                    } else if (!$item->get('isSelected')->getData() && in_array($item->get('inspectionItem')->getData(), $inspectionItemList)) {
                        $deliveryInspectionDetails->removeElement($item->getData());
                    }
                }
                $deliveryInspectionHeader->setDeliveryInspectionDetails($deliveryInspectionDetails);
                
                $options['service']->finalize($deliveryInspectionHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DeliveryInspectionHeader::class,
        ));
        $resolver->setRequired(array('service', 'init', 'inspectionItemRepository'));
    }
}
