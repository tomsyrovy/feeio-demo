<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Client;
use AppBundle\Entity\Company;
use AppBundle\Entity\Source;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CampaignFormType extends AbstractType
{

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * CampaignFormType constructor.
	 *
	 * @param \AppBundle\Entity\Client $client
	 */
	public function __construct( \AppBundle\Entity\Client $client ){
		$this->client = $client;
	}

	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('nameOwn', 'text', [
		    'label' => 'Název',
		    'required' => false,
	    ]);
	    $builder->add('year', 'entity', array(
		    'label' => 'Rok',
		    'class' => 'AppBundle\Entity\Year',
		    'property' => 'year',
	    ));
	    $builder->add('companyGroup', 'entity', array(
		    'label' => 'Skupina',
		    'class' => 'AppBundle\Entity\CompanyGroup',
		    'property' => 'name',
		    'choices' => $this->client->getCompany()->getCompanyGroupsEnabled(),
	    ));
	    $builder->add('contactPersonList', new ContactPersonListFormType(), array(
		    'label' => 'Kontaktní osoby',
		    'cascade_validation' => true,
	    ));
	    $builder->add('sourceList', new SourceListFormType($this->client->getJobPositions()), array(
		    'label' => 'Zdroje',
		    'cascade_validation' => true,
	    ));
	    $builder->add('campaignManagers', 'collection', [
		    'label' => 'Oprávnění',
		    'type' => new CampaignManagerFormType($this->client->getCompany()),
		    'allow_add' => true,
		    'allow_delete' => true,
		    'by_reference' => false,
		    'prototype' => true,
		    'prototype_name' => 'campaignManager__name__',
		    'cascade_validation' => true,
	    ]);

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Campaign',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_campaign';
    }
}
