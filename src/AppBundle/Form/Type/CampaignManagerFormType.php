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

class CampaignManagerFormType extends AbstractType
{

	/**
	 * @var Company
	 */
	private $company;

	/**
	 * CampaignManagerFormType constructor.
	 *
	 * @param \AppBundle\Entity\Company $company
	 */
	public function __construct( Company $company ){
		$this->company = $company;
	}


	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('userCompany', 'entity', array(
		    'label' => 'Uživatel',
		    'class' => 'AppBundle\Entity\UserCompany',
		    'choices' => $this->company->getUserCompanyRelationsOfTemporalityStatus('enabled'),
		    'choice_label' => function ($userCompany) {
			    return $userCompany->getUser()->getFullName();
		    },
	    ));
	    $builder->add('owner', 'checkbox', [
		    'label' => 'Vlastník',
		    'required' => false,
	    ]);
	    $builder->add('jobManager', 'checkbox', [
		    'label' => 'Manažer',
		    'required' => false,
	    ]);
	    $builder->add('jobConsultant', 'checkbox', [
		    'label' => 'Pracovník',
		    'required' => false,
	    ]);


    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CampaignManager',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_campaignmanager';
    }
}
