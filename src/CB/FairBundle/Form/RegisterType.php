<?php
/**
 * Created by: cbaker
 * Date: 7/5/13
 */

namespace CB\FairBundle\Form;


use CB\FairBundle\Form\AuctionItemType;
use CB\FairBundle\Form\BakedItemType;
use Doctrine\Tests\ORM\Tools\Pagination\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * TODO: Create the build form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bakedItems', 'collection', array(
                'type' => new BakedItemType(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ))
            ->add('auctionItems', 'collection', array(
                'type' => new AuctionItemType(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ))
            ->add('submit', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => 'CB\UserBundle\Entity\User',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'cb_registration';
    }
}