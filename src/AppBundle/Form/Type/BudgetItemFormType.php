<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BudgetItemFormType extends AbstractType
{

    /**
     * @var
     */
    private $yearmonths;

    /**
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
            ->add('name', 'text', array(
                    'required' => false
                )
            )
            ->add('pricePlan', 'money', array(
                    'required' => false,
                    'currency' => 'CZK',
                    'scale' => 0,
                )
            )
            ->add('priceReal', 'money', array(
                    'required' => false,
                    'currency' => 'CZK',
                    'scale' => 0,
                )
            )
            ->add('yearmonthPlan', 'entity', array(
                    'label' => 'Měsíc',
                    'class' => 'AppBundle\Entity\YearMonth',
                    'placeholder' => 'žádné',
                    'choices' => $this->yearmonths,
                    'property' => 'code',
                    'required' => false
                )
            )
            ->add('yearmonthReal', 'entity', array(
                    'label' => 'Měsíc',
                    'class' => 'AppBundle\Entity\YearMonth',
                    'placeholder' => 'žádné',
                    'choices' => $this->yearmonths,
                    'property' => 'code',
                    'required' => false
                )
            )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\BudgetItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_budgetitem';
    }
}
