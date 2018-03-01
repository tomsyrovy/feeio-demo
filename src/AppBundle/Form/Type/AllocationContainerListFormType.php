<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Commission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllocationContainerListFormType extends AbstractType
{

    /**
     * @var Commission
     */
    private $commission;

    /**
     * @var AllocationContainer
     */
    private $allocationContainer;

    /**
     * AllocationContainerFormType constructor.
     *
     * @param \AppBundle\Entity\Commission          $commission
     * @param \AppBundle\Entity\AllocationContainer $allocationContainer
     */
    public function __construct( \AppBundle\Entity\Commission $commission, \AppBundle\Entity\AllocationContainer $allocationContainer ){
        $this->commission          = $commission;
        $this->allocationContainer = $allocationContainer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'label' => 'Název seznamu',
            'required' => false,
        ]);
        $builder->add('allocationContainerListItems', 'collection', array(
            'label' => 'Položka',
            'type' => new AllocationContainerListItemFormType($this->commission, $this->allocationContainer),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => 'allocationContainerListItem__name2__',
            'cascade_validation' => true,
        ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AllocationContainerList'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_allocationcontainerlist';
    }
}
