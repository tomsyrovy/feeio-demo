<?php
	/**
	 * Project: feeio2
	 * File: WidgetDO.php
	 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
	 * Date: 05.03.16
	 * Version: 1.0
	 */

	namespace AppBundle\DependencyInjection\Widget;

	use AppBundle\Entity\Widget;

	class WidgetDO {

		/**
		 * @var Widget
		 */
		private $widget;

		/**
		 * @var array
		 */
		private $result;

		/**
		 * WidgetDO constructor.
		 *
		 * @param \AppBundle\Entity\Widget $widget
		 * @param array                    $result
		 */
		public function __construct( \AppBundle\Entity\Widget $widget, array $result ){
			$this->widget = $widget;
			$this->result = $result;
		}

		/**
		 * @return Widget
		 */
		public function getWidget(){
			return $this->widget;
		}

		/**
		 * @return array
		 */
		public function getResult(){
			return $this->result;
		}

	}