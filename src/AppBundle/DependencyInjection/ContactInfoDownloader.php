<?php
/**
 * Project: feeio2
 * File: CompanyInfoDownloader.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 05.12.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection;


class ContactInfoDownloader {

	/**
	 * Vrátí pole informací o kontaktu dle zadaného IČ
	 *
	 * @param $ico
	 *
	 * @return array
	 */
	public static function getInfoAboutContact($ico){

		$ARES = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ico=';

		$ico = intval($ico);
		$file = @file_get_contents($ARES.$ico);
		if ($file) $xml = @simplexml_load_string($file);
		$a = array();
		if ($xml) {
			$ns = $xml->getDocNamespaces();
			$data = $xml->children($ns['are']);
			$el = $data->children($ns['D'])->VBAS;
			if (strval($el->ICO) == $ico) {
				$a['vatnumber'] 	= strval($el->ICO);
				$a['taxnumber'] 	= strval($el->DIC);
				$a['title'] 	= strval($el->OF);
				$a['street']	= strval($el->AA->NU);
				$a['number'] = strval($el->AA->CD);
				$a['city']	= strval($el->AA->N);
				$a['zipcode']	= strval($el->AA->PSC);
				$a['stav'] 	= 'ok';
			} else
				$a['stav'] 	= 'IČ kontaktu nebylo nalezeno.';
		} else
			$a['stav'] 	= 'Databáze ARES není dostupná.';

		return $a;

	}

}