<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SourceFormType extends AbstractType
{

    /**
     * @var
     */
    private $choices;

    /**
     * SourceFormType constructor.
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
            ->add('jobPosition', 'entity', [
                'label' => 'Pracovní pozice',
                'class' => 'AppBundle\Entity\JobPosition',
                'property' => 'name',
                'choices' => $this->choices,
            ])
            ->add('rateExternal', 'money', [
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
            'data_class' => 'AppBundle\Entity\Source'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_source';
    }
}
