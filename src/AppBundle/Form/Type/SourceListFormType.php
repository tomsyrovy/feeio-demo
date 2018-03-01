<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SourceListFormType extends AbstractType
{

    private $choices;

    /**
     * SourceListFormType constructor.
     *
     * @param $choices
     */
    public function __construct( $choices ){
        $this->choices = $choices;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('sources', 'collection', array(
	        'label' => 'Zdroje',
            'type' => new SourceFormType($this->choices),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => 'source__name__',
            'cascade_validation' => true,
        ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SourceList'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_sourceList';
    }
}
