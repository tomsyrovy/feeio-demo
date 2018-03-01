<?php
	/**
	 * Project: feeio2
	 * File: ReportConfigurationFactory.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 12.08.16
	 * Version: 1.0
	 */

	namespace AppBundle\Entity\Factory;


	use AppBundle\Entity\ReportConfiguration;

	class ReportConfigurationFactory {

		/**
		 * @param string $code
		 * @param string $name
		 * @param string $description
		 * @param array  $fields
		 *
		 * @return \AppBundle\Entity\ReportConfiguration
		 */
		public function createReportConfiguration(
			$code,
			$name,
			$description,
			array $fields
		)
		{
			$reportConfiguration = new ReportConfiguration();
			$reportConfiguration->setCode($code);
			$reportConfiguration->setName($name);
			$reportConfiguration->setDescription($description);
			$reportConfiguration->setFields($fields);

			return $reportConfiguration;

		}

	}