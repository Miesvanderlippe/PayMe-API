<?php

/*
	TODO : 
	- Starttransaction 
		- Do a call
		- Database interaction

*/

require_once(APPPATH . "classes/crypt.php");

class Payme extends Crypt{

	protected static $email	 = "71989@ict-lab.nl";
	protected static $pmid	 = "86nmb6fonm";
	protected static $pmkey	 = "ikjw6gux6954m3cjgaj5d77rs70ncbey";
	
	private function __construct(){}

	public static function GetBankList(){

		$jsonurl	 = "http://payme.ict-lab.nl/api/banklist/";
		$json  		 = file_get_contents($jsonurl);
		$banklist 	 = json_decode($json);

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

	public static function StartTransaction($amount, $bankID, $purchaseID, $description, $returnURL, $failURL){

		$returnURL	 = Self::SpecialUrlEncode($returnURL);
		$failURL	 = Self::SpecialUrlEncode($failURL);

		$verifcationKey = sha1(Self::pmid . Self::pmkey . $purchaseID . $amount);

		$url = "http://payme.ict-lab.nl/api/starttrans/" . Self::pmid . "/" . Self::pmkey . "/" . $amount . "/" . $bankID . "/" . $purchaseID . "/" . $description . "/" . $returnURL . "/" . $failURL. "/" . $verifcationKey . "/";
	}

	public static function GetTransactionStatus(){


	}
}