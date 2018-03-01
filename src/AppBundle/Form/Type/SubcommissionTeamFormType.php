<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Subcommission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubcommissionTeamFormType extends AbstractType
{

    /**
     * @var Subcommission
     */
    private $subcommission;

    /**
     * SubcommissionTeamFormType constructor.
     *
     * @param \AppBundle\Entity\Subcommission $subcommission
     */
    public function __construct( Subcommission $subcommission ){
        $this->subcommission = $subcommission;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $company = $this->subcommission->getCommission()->getCompany();

        $userCompanyRelations = $company->getUserCompanyRelationsOfTemporalityStatus('enabled');

        $builder->add('members', 'collection',
            array(
                'type' => new SubcommissionTeamUserCompanyFormType($userCompanyRelations),
                'label' => 'Tým',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => 'usercompany__name__',
                'cascade_validation' => true,
            )
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SubcommissionTeam'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_subcommissionteam';
    }
}
