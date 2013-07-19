<?php

namespace CB\FairBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CB\FairBundle\Form\TimeType;

class BoothType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('location')
            ->add('workerLimit')
            ->add('times', 'collection', array(
                'type' => new TimeType(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CB\FairBundle\Entity\Booth'
        ));
    }

    public function getName()
    {
        return 'cb_fairbundle_boothtype';
    }
}
