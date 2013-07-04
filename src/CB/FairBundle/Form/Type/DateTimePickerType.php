<?php
/**
 * Created by: cbaker
 * Date: 7/3/13
 */

namespace CB\FairBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimePickerType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateOptions = $builder->get('date')->getOptions();

        $builder->remove('date')
            ->add('date', 'datePicker', $dateOptions)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
            'date_input' => '',
        ));
    }

    /**
     * @return string The name of the parent type
     */
    public function getParent()
    {
        return 'datetime';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'dateTimePicker';
    }
}