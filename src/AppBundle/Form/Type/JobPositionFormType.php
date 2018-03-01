<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JobPositionFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Název',
                'required' => false
            ])
            ->add('nameExternal', 'text', [
                'label' => 'Externí název',
                'required' => false
            ])
            ->add('internalRate', 'money', [
                'label' => 'Interní sazba',
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ])
            ->add('externalRate', 'money', [
                'label' => 'Externí sazba',
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\JobPosition'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_jobposition';
    }
}
