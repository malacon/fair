<?php

namespace CB\FairBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', 'dateTimePicker', array())
            ->add('duration')
            ->add('workerLimit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CB\FairBundle\Entity\Time'
        ));
    }

    public function getName()
    {
        return 'cb_fairbundle_timetype';
    }
}
