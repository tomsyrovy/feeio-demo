<?php

namespace AppBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UserBundle\Entity\User;

class ContactFormType extends AbstractType
{

	/**
	 * @var User
	 */
	private $user;

	private $url;

	/**
	 * @var ArrayCollection
	 */
	private $companies;

	/**
	 * ContactFormType constructor.
	 *
	 * @param \UserBundle\Entity\User                      $user
	 * @param                                              $url
	 * @param \Doctrine\Common\Collections\ArrayCollection $companies
	 */
	public function __construct( User $user, $url, ArrayCollection $companies ) {
		$this->user = $user;
		$this->url = $url;
		$this->companies = $companies;
	}


	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('company', 'entity', array(
		        'label' => 'Společnost',
		        'class' => 'AppBundle\Entity\Company',
		        'choices' => $this->companies,
		        'property' => 'name'
	        ))
	        ->add('type', 'entity', array(
		        'label' => 'Typ kontaktu',
		        'class' => 'AppBundle\Entity\ContactType',
		        'property' => 'title',
	        ))
            ->add('vatnumber', 'text', array(
	            'label' => 'IČ',
	            'required' => false,
	            'attr' => array(
		            'url' => $this->url,
	            )
            ))
	        ->add('taxnumber', 'text', array(
		        'label' => 'DIČ',
		        'required' => false,
	        ))
            ->add('title', 'text', array(
                'label' => 'Název',
	            'required' => false,
            ))
            ->add('street', 'text', array(
	            'label' => 'Ulice',
	            'required' => false,
            ))
            ->add('number', 'text', array(
	            'label' => 'Číslo popisné',
	            'required' => false,
            ))
            ->add('city', 'text', array(
	            'label' => 'Město',
	            'required' => false,
            ))
            ->add('zipcode', 'text', array(
	            'label' => 'PSČ',
	            'required' => false,
            ))
	        ->add('country', 'entity', array(
		        'label' => 'Země',
		        'class' => 'AppBundle\Entity\ContactCountry',
		        'query_builder' => function (EntityRepository $er) {
			        $allChoices = $er->createQueryBuilder('c')->addSelect('(CASE WHEN c.iso = \'CZ\' THEN 2 ELSE (CASE WHEN c.iso = \'SK\' THEN 1 ELSE 0 END) END) AS HIDDEN sort')->orderBy('sort', 'DESC')->addOrderBy('c.name', 'ASC');
			        return $allChoices;
		        },
		        'property' => 'name',
	        ))
            ->add('website', 'text', array(
	            'label' => 'Internetové stránky',
	            'required' => false,
            ))
            ->add('email', 'email', array(
	            'label' => 'E-mail',
	            'required' => false,
            ))
            ->add('telephone', 'text', array(
	            'label' => 'Telefon',
	            'required' => false,
            ))
	        ->add('bankaccountnumber', 'text', array(
		        'label' => 'Číslo bankovního účtu',
		        'required' => false,
	        ))
	        ->add('iban', 'text', array(
		        'label' => 'IBAN',
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
            'data_class' => 'AppBundle\Entity\Contact'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_contact';
    }
}
