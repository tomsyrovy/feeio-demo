<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactPersonFormType extends AbstractType
{


	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', [
            'label' => 'Křestní jméno',
            'required' => false,
        ]);
        $builder->add('lastName', 'text', [
            'label' => 'Příjmení',
            'required' => false,
        ]);
        $builder->add('position', 'text', [
            'label' => 'Pozice',
            'required' => false,
        ]);
        $builder->add('email', 'text', [
            'label' => 'E-mail',
            'required' => false,
        ]);
        $builder->add('telephone', 'text', [
            'label' => 'Telefon',
            'required' => false,
        ]);

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ContactPerson'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_contactPerson';
    }
}
