<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvoiceItemFormType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('description', 'text', array(
                    'required' => false,
                    'label' => 'Popis',
                )
            )
            ->add('price', 'money', array(
                    'required' => false,
                    'currency' => 'CZK',
                    'scale' => 0,
                    'label' => 'Částka',
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
            'data_class' => 'AppBundle\Entity\InvoiceItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_invoiceitem';
    }
}
