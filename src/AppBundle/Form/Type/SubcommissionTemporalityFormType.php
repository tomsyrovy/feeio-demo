<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubcommissionTemporalityFormType extends AbstractType
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

        $builder
            ->add('subcommission', new SubcommissionFormType($this->yearmonths), array(

            ));

        $builder
            ->add('feeFixPlan', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ))
            ->add('feeFixReal', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ))
            ->add('feeSuccessPlan', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ))
            ->add('feeSuccessReal', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ))
            ->add('hoursPlan', 'integer', array(
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
            'data_class' => 'AppBundle\Entity\SubcommissionTemporality'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_subcommissiontemporality';
    }
}
