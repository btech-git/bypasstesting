<?php

namespace AppBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Doctrine\Common\Collections\Criteria;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Common\UserRole;

class StaffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('position')
            ->add('joinDate', 'date')
            ->add('email')
            ->add('address')
            ->add('phone')
            ->add('note')
            ->add('userRoles', EntityType::class, array(
                'class' => UserRole::class,
                'choice_label' => 'name',
                'choice_attr' => function($role) {
                    return array('data-level' => $role->getLevel(), 'data-order' => $role->getOrdinal());
                },
                'label' => 'Roles',
                'expanded' => true,
                'multiple' => true,
                'choices' => $options['userRoleRepository']->findBy(array(), array('ordinal' => Criteria::ASC)),
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $staff = $event->getData();
                $form = $event->getForm();
                if (empty($staff->getId())) {
                    $form->add('username');
                    $form->add('plainPassword', RepeatedType::class, array(
                        'mapped' => false,
                        'constraints' => array(new NotBlank(), new Length(array('min' => '8'))),
                        'type' => PasswordType::class,
                        'first_options'  => array('label' => 'New Password'),
                        'second_options' => array('label' => 'Confirm Password'),
                    ));
                } else {
                    $form->add('isActive');
                }
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $staff = $event->getData();
                $form = $event->getForm();
                if (empty($staff->getId())) {
                    $plainPassword = $form->get('plainPassword')->getData();
                    $password = $options['encoder']->encodePassword($staff, $plainPassword);
                    $staff->setPassword($password);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Staff::class,
        ));
        $resolver->setRequired(array('encoder', 'userRoleRepository'));
    }
}
