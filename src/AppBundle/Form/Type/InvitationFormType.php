<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvitationFormType extends AbstractType
{

    /**
     * @var Company
     */
    private $company;

    public function __construct( $company ) {
        $this->company = $company;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'E-mail uživatele',
                'required' => false,
            ))
            ->add('rateInternal', 'money', array(
                'label' => 'Interní sazba',
                'required' => false,
                'currency' => 'CZK',
            ))
            ->add('hours', 'integer', array(
                'label' => 'Časová kapacita',
                'required' => false,
            ))
            ->add('role', 'entity', array(
                'label' => 'Role',
                'class' => 'AppBundle\Entity\Role',
                'choices' => $this->company->getRolesEnabled(),
                'property' => 'name'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Invitation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_invitation';
    }
}
