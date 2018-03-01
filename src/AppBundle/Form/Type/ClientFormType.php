<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Company;
use AppBundle\Entity\SourceList;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientFormType extends AbstractType
{

	/**
	 * @var ArrayCollection
	 */
	private $companies;

	/**
	 * ClientFormType constructor.
	 *
	 * @param \Doctrine\Common\Collections\ArrayCollection $companies$contacts
	 */
	public function __construct( ArrayCollection $companies){
		$this->companies = $companies;
	}


	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('company', 'entity', array(
	        'label' => 'Společnost',
	        'class' => 'AppBundle\Entity\Company',
	        'choices' => $this->companies,
	        'property' => 'name'
        ));
	    $builder->add('name', 'text', array(
		    'label' => 'Název klienta',
		    'required' => false,
	    ));
	    $builder->add('code', 'text', array(
		    'label' => 'Kód klienta',
		    'required' => false,
	    ));
	    $builder->add('contactPersonList', new ContactPersonListFormType(), array(
		    'label' => 'Kontaktní osoby',
		    'cascade_validation' => true,
	    ));

	    $formModifier = function (FormInterface $form, Company $company = null) {

		    if($company === null){
			    $contacts = [];
			    $jobPositions = [];
		    }else{
			    $contacts = $company->getContactsOfTypeCode('subscriber');
			    $jobPositions = $company->getJobPositionsEnabled();
		    }

		    $form->add('contact', 'entity', array(
			    'label' => 'Obchodní název',
			    'class' => 'AppBundle\Entity\Contact',
			    'choices' => $contacts,
			    'property' => 'title'
		    ));

		    $form->add('sourceList', new SourceListFormType($jobPositions), array(
			    'label' => 'Smluvní podmínky',
			    'cascade_validation' => true,
		    ));

	    };

	    $builder->addEventListener(
		    FormEvents::PRE_SET_DATA,
		    function (FormEvent $event) use ($formModifier) {

			    $data = $event->getData();

			    $company = $data->getCompany();

			    $formModifier($event->getForm(), $company);
		    }
	    );

	    $builder->get('company')->addEventListener(
		    FormEvents::POST_SUBMIT,
		    function (FormEvent $event) use ($formModifier) {
			    // It's important here to fetch $event->getForm()->getData(), as
			    // $event->getData() will get you the client data (that is, the ID)
			    $company = $event->getForm()->getData();

			    // since we've added the listener to the child, we'll have to pass on
			    // the parent to the callback functions!
			    $formModifier($event->getForm()->getParent(), $company);
		    }
	    );

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_client';
    }
}
