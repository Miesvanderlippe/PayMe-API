<?php

/*
	TODO : 
	- Starttransaction 
		- Do a call
		- Database interaction
			- After starting transaction

*/

require_once(APPPATH . "classes/crypt.php");

class Payme extends Crypt{

	const email	 = "71989@ict-lab.nl";
	const pmid	 = "86nmb6fonm";
	const pmkey	 = "ikjw6gux6954m3cjgaj5d77rs70ncbey";
	
	private function __construct(){}

	private static function CurlGet($url){

		$ch		 = curl_init();
		$options = array(
		    CURLOPT_SSL_VERIFYPEER => false,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_URL            => $url
		);

		curl_setopt_array($ch, $options);

		$data 	 = curl_exec($ch);
		
		curl_close($ch);

		return $data;
	}

	public static function GetBankList(){

		$jsonurl	 = "http://payme.ict-lab.nl/api/banklist/";
		$json  		 = file_get_contents($jsonurl);
		$banklist 	 = self::CurlGet($json, TRUE);

		return $banklist;
	}

	public static function SpecialUrlEncode($url){

		$replacedURL	 = str_replace("/", "#", $url);
		$encodedURL 	 = urlencode($replacedURL);

		return $encodedURL;
	}

	public static function SpecialUrlDecode($url){

		$decodedURL		 =	urldecode($url);
		$replacedURL	 =	str_replace("#", "/", $decodedURL);

		return $replacedURL;
	}

	public static function StartTransactionURL($amount, $bankID, $purchaseID, $description, $returnURL, $failURL){

		$returnURL	 = self::SpecialUrlEncode($returnURL);
		$failURL	 = self::SpecialUrlEncode($failURL);

		$verifcationKey = sha1(self::pmid . self::pmkey . $purchaseID . $amount);

		$url = "http://payme.ict-lab.nl/api/starttrans/" . self::pmid . "/" . self::pmkey . "/" . $amount . "/" . $bankID . "/" . $purchaseID . "/" . $description . "/" . $returnURL . "/" . $failURL. "/" . $verifcationKey . "/";

		return $url;
	}

	public static function GetTransactionStatus($transactionID, $sha1){

		$URL = 'http://payme.ict-lab.nl/api/statusrequest/PWiRkJiv/2a37726251599c6ad50ca0aec02e09e02bd3c3f4';

		$URL = $URL . $transactionID . '/' . $sha1 . '/'; 

		$data = self::CurlGet($URL);

		$dataArray = json_decode($data, true);

		return $dataArray;
	}
}