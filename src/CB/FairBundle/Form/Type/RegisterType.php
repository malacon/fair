<?php
/**
 * Created by: cbaker
 * Date: 7/5/13
 */

namespace CB\FairBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'registration';
    }
}