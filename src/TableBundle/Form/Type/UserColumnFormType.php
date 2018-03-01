<?php

namespace TableBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserColumnFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formula', 'text', array(
                'required' => false,
            ))
            ->add('title', 'text', array(
                'required' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TableBundle\Entity\UserColumn'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tablebundle_usercolumn';
    }
}
