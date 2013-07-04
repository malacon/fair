<?php
/**
 * Created by: cbaker
 * Date: 7/3/13
 */

namespace CB\FairBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatePickerType extends AbstractType{

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
           'widget' => 'single_text',
        ));
    }

    /**
     * @return string The name of the parent type
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'datePicker';
    }
}