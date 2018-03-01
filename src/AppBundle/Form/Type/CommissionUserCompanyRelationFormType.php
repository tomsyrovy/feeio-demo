<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommissionUserCompanyRelationFormType extends AbstractType
{

    /**
     * @var array
     */
    private $choices;

    /**
     * CommissionUserCompanyRelationFormType constructor.
     *
     * @param array $choices
     */
    public function __construct( array $choices ){
        $this->choices = $choices;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userCompany', 'entity', array(
                'label' => 'Uživatel',
                'class' => 'AppBundle\Entity\UserCompany',
                'property' => 'fullname',
                'choices' => $this->choices,
            ))
            ->add('commissionUserCompanyRelationType', 'entity', array(
                'label' => 'Oprávnění',
                'class' => 'AppBundle\Entity\CommissionUserCompanyRelationType',
                'property' => 'name',
                'multiple' => false,
                'expanded' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CommissionUserCompanyRelation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_commissionusercompanyrelation';
    }
}
