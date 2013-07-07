<?php

namespace CB\FairBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numberOfTimes')
            ->add('numberOfBakedItems')
            ->add('numberOfAuctionItems')
            ->add('description')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CB\FairBundle\Entity\Rule'
        ));
    }

    public function getName()
    {
        return 'cb_fairbundle_ruletype';
    }
}
