<?php

namespace CB\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FamilyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('password')
            ->add('lastLogin')
            ->add('locked')
            ->add('expired')
            ->add('roles')
            ->add('name')
            ->add('eldest')
            ->add('eldestGrade')
            ->add('isPassedRules')
            ->add('maxHours')
            ->add('timeToLogin', 'datetime', array(
                'input'  => 'datetime',
                'widget' => 'choice',
            ))
            ->add('bakedItem')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CB\UserBundle\Entity\Family'
        ));
    }

    public function getName()
    {
        return 'cb_userbundle_familytype';
    }
}
