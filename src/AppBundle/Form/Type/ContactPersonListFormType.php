<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactPersonListFormType extends AbstractType
{


	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contactPersons', 'collection', array(
	        'label' => 'Kontaktní osoby',
            'type' => new ContactPersonFormType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => 'contactPerson__name__',
            'cascade_validation' => true,
        ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ContactPersonList'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_contactPersonList';
    }
}
