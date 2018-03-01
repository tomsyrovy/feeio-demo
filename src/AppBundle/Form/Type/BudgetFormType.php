<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BudgetFormType extends AbstractType
{

    /**
     * @var
     */
    private $yearmonths;

    /**
     * SubcommissionFormType constructor.
     *
     * @param $yearmonths
     */
    public function __construct( $yearmonths ){
        $this->yearmonths = $yearmonths;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array(
            'required' => false,
        ));
        $builder->add('budgetItems', 'collection', array(
                'type' => new BudgetItemFormType($this->yearmonths),
                'label' => 'Položky',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => 'budgetitem__name__',
            )
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Budget'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_budget';
    }
}
