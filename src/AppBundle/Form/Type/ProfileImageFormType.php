<?php
/**
 * Project: feeio2   
 * File: ProfileFormType.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 13.03.15
 * Version: 1.0
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileImageFormType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		parent::buildForm($builder, $options);

		$builder->add('image', new FileFormType(), array(
			'label' => 'Profilový obrázek'
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'UserBundle\Entity\User',
			'intention'  => 'profile',
		));
	}

	public function getName()
	{
		return 'user_profile_image';
	}
}