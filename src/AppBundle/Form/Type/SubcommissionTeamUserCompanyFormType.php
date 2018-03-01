<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubcommissionTeamUserCompanyFormType extends AbstractType
{

    /**
     * @var
     */
    private $choices;

    /**
     * CommissionUserCompanyRelationFormType constructor.
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

        $builder
            ->add('userCompany', 'entity', array(
                'label' => 'Uživatel',
                'class' => 'AppBundle\Entity\UserCompany',
                'property' => 'fullname',
                'choices' => $this->choices,
            ))
        ;

        $builder
            ->add('rateInternal', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ))
            ->add('rateExternal', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ))
            ->add('hours', 'integer', array(
                'required' => false,
                'scale' => 0,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SubcommissionTeamUserCompany'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_subcommissionteamusercompany';
    }
}
