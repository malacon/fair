<?php

namespace CB\UserBundle\Form;

use CB\FairBundle\Entity\AuctionItem;
use CB\FairBundle\Entity\BakedItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('lastLogin')
            ->add('familyName')
            ->add('children')
            ->add('times')
            ->add('bakedItems', 'collection', array(
                'type' => new BakedItem(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('auctionItems', 'collection', array(
                'type' => new AuctionItem(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CB\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'cb_userbundle_usertype';
    }
}
