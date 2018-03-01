<?php
	/**
	 * Project: feeio2
	 * File: SumarizationDataObject.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 29.12.16
	 * Version: 1.0
	 */

	namespace AppBundle\DataObject;


	class SumarizationDataObject {

		private $quantityPlan = 1;

		private $buyingPricePlan = 0;

		private $sellingPricePlan = 0;

		private $profitPlan = 0;

		private $quantityReal = 1;

		private $buyingPriceReal = 0;

		private $sellingPriceReal = 0;

		private $profitReal = 0;

		/**
		 * @return int
		 */
		public function getQuantityPlan(){
			return $this->quantityPlan;
		}

		/**
		 * @param int $quantityPlan
		 */
		public function setQuantityPlan( $quantityPlan ){
			$this->quantityPlan = $quantityPlan;
		}

		/**
		 * @return int
		 */
		public function getBuyingPricePlan(){
			return $this->buyingPricePlan;
		}

		/**
		 * @param int $buyingPricePlan
		 */
		public function setBuyingPricePlan( $buyingPricePlan ){
			$this->buyingPricePlan = $buyingPricePlan;
		}

		/**
		 * @return int
		 */
		public function getSellingPricePlan(){
			return $this->sellingPricePlan;
		}

		/**
		 * @param int $sellingPricePlan
		 */
		public function setSellingPricePlan( $sellingPricePlan ){
			$this->sellingPricePlan = $sellingPricePlan;
		}

		/**
		 * @return int
		 */
		public function getProfitPlan(){
			return $this->profitPlan;
		}

		/**
		 * @param int $profitPlan
		 */
		public function setProfitPlan( $profitPlan ){
			$this->profitPlan = $profitPlan;
		}

		/**
		 * @return int
		 */
		public function getQuantityReal(){
			return $this->quantityReal;
		}

		/**
		 * @param int $quantityReal
		 */
		public function setQuantityReal( $quantityReal ){
			$this->quantityReal = $quantityReal;
		}

		/**
		 * @return int
		 */
		public function getBuyingPriceReal(){
			return $this->buyingPriceReal;
		}

		/**
		 * @param int $buyingPriceReal
		 */
		public function setBuyingPriceReal( $buyingPriceReal ){
			$this->buyingPriceReal = $buyingPriceReal;
		}

		/**
		 * @return int
		 */
		public function getSellingPriceReal(){
			return $this->sellingPriceReal;
		}

		/**
		 * @param int $sellingPriceReal
		 */
		public function setSellingPriceReal( $sellingPriceReal ){
			$this->sellingPriceReal = $sellingPriceReal;
		}

		/**
		 * @return int
		 */
		public function getProfitReal(){
			return $this->profitReal;
		}

		/**
		 * @param int $profitReal
		 */
		public function setProfitReal( $profitReal ){
			$this->profitReal = $profitReal;
		}

	}