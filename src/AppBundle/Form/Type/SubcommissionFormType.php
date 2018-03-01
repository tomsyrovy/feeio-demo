<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubcommissionFormType extends AbstractType
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
            ->add('yearmonth', 'entity', array(
                'label' => 'Měsíc',
                'class' => 'AppBundle\Entity\YearMonth',
                'choices' => $this->yearmonths,
                'property' => 'code',
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Subcommission'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_subcommission';
    }
}
