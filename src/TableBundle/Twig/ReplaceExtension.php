<?php

namespace TableBundle\Twig;

/**
* A TWIG Extension which allows to show Controller and Action name in a TWIG view.
*
* The Controller/Action name will be shown in lowercase. For example: 'default' or 'index'
*
*/
class ReplaceExtension extends \Twig_Extension
{
	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('replaceCalcx', array($this, 'replaceCalcx')),
		);
	}

	public function replaceCalcx($formula, $index)
	{
		$formula = preg_replace("/(?<=[A-Z])(0)/", $index, $formula);

		return $formula;
	}

	public function getName()
	{
		return 'table_replace_extension';
	}
}