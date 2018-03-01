<?php
/**
 * Project: feeio2   
 * File: FileFormType.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 14.03.15
 * Version: 1.0
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		parent::buildForm($builder, $options);
		
		$builder->add('file', 'file', array(
			'label' => 'Soubor',
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\File',
			'intention'  => 'file',
		));
	}

	public function getName()
	{
		return 'app_file';
	}
}