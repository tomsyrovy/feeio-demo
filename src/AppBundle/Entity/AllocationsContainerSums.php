<?php
	/**
	 * Project: feeio2
	 * File: AbstractAllocationsContainerHierarchy.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 12.12.16
	 * Version: 1.0
	 */

	namespace AppBundle\Entity;


	class AllocationsContainerSums {

		private $quantityPlan = 1;

		private $buyingPricePlan = 0;

		private $sellingPricePlan = 0;

		private $quantityReal = 1;

		private $buyingPriceReal = 0;

		private $sellingPriceReal = 0;

		private $invoicePrice = 0;

		private $overServiceItems = [];

		public function sumarize($children){

			foreach($children as $child){

				$this->setBuyingPricePlan($this->getBuyingPricePlan()+$child->getBuyingPricePlan()*$child->getQuantityPlan());
				$this->setSellingPricePlan($this->getSellingPricePlan()+$child->getSellingPricePlan()*$child->getQuantityPlan());

				$this->setBuyingPriceReal($this->getBuyingPriceReal()+$child->getBuyingPriceReal()*$child->getQuantityReal());
				$this->setSellingPriceReal($this->getSellingPriceReal()+$child->getSellingPriceReal()*$child->getQuantityReal());

				if(method_exists ( $child , 'getInvoicePrice' )){
					$this->setInvoicePrice($this->getInvoicePrice()+$child->getInvoicePrice());
				}

				if(method_exists ( $child , 'getOverService' )){
					$this->overServiceItems[] = $child->getOverService();
				}

			}

		}

		public function getProfitPlan(){
			return $this->getSellingPricePlan()-$this->getBuyingPricePlan();
		}

		public function getProfitReal(){
			return $this->getInvoicePrice()-$this->getBuyingPriceReal();
		}

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
		public function getInvoicePrice(){
			return $this->invoicePrice;
		}

		/**
		 * @param int $invoicePrice
		 */
		public function setInvoicePrice( $invoicePrice ){
			$this->invoicePrice = $invoicePrice;
		}

		/**
		 * @return integer
		 */
		public function getOverService(){

			if(count($this->overServiceItems) === 0){
				return 0;
			}

			return array_sum($this->overServiceItems)/count($this->overServiceItems);

		}



	}