<?php
/**
 * Project: feeio2
 * File: CostFormFilterType.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 02.03.16
 * Version: 1.0
 */

namespace AppBundle\Form\Filter\Type;

use AppBundle\Form\Type\CostFormType;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CostFormFilterType extends CostFormType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$options['required'] = false;

		parent::buildForm($builder, $options);

		foreach($builder->all() as $child){

			$type = $child->getType()->getName();
			$options = $child->getOptions();

			switch($type){
				case "entity" : {
					$newType = "filter_entity";
					$newOptions = $options;
//					VarDumper::dump($newOptions);exit;
				}break;
				case "money" :
				case "percent" : {
					$newType = "filter_number";
					$newOptions = [
						'condition_operator' => FilterOperands::OPERAND_SELECTOR,
					];
				}break;
				case "text" : {
					$newType = "filter_text";
					$newOptions = [
						'condition_pattern' => FilterOperands::OPERAND_SELECTOR,
					];
				}break;
				default : {
					$newType = $type;
					$newOptions = $options;
				}break;
			}

			$builder->add($child->getName(), $newType, $newOptions);

		}

		$this->addEventListeners($builder);

//		VarDumper::dump($builder->get('yearmonthReal')->getOptions());exit;

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'csrf_protection'   => false,
			'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
		));
	}

}