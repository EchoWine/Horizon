<?php

namespace Work\Collection;

use CoreWine\Component\Collection as BaseCollection;

class InvoiceCollection extends BaseCollection{

	/**
	 * Get total net
	 *
	 * @return float
	 */
	public function getTotalPriceNet(){
		return $this -> sum(function($value){
			return $value -> getPriceNet();
		});
	}

	/**
	 * Get total gross
	 *
	 * @return float
	 */
	public function getTotalPriceGross(){
		return $this -> sum(function($value){
			return $value -> getPriceGross();
		});
	}

	/**
	 * Get total invoices
	 *
	 * @return float
	 */
	public function getCount(){
		return $this -> count();
	}

	/**
	 * Get total taxes
	 *
	 * @return float
	 */
	public function getTotalTaxes(){
		return$this -> getTotalPriceNet() * 0.21;
	}
}