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
		$json  		 = self::CurlGet($jsonurl);
		$banklist 	 = json_decode($json, TRUE);

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

		$returnURL	 = self::SpecialUrlEncode($returnURL);
		$failURL	 = self::SpecialUrlEncode($failURL);

		$verifcationKey = sha1(self::pmid . self::pmkey . $purchaseID . $amount);

		$url = 'http://payme.ict-lab.nl/api/starttrans/' . self::pmid . '/' . self::pmkey . '/' . $amount . '/' . $bankID . '/' . $purchaseID . '/' . urlencode($description) . '/' . $returnURL . '/' . $failURL. '/' . $verifcationKey . '/';

		$data = self::CurlGet($url);
		$data = json_decode($data, true);

		if($data['sha1'] == $verifcationKey)
			$data['keyMatch'] = true;
		else
			$data['keyMatch'] = false;

		$data['fwdurl'] = urldecode($data['fwdurl']);

		return $data;
	}

	public static function GetTransactionStatus($transactionID, $sha1){

		$url = 'http://payme.ict-lab.nl/api/statusrequest/';

		$url = $url . $transactionID . '/' . $sha1 . '/'; 

		$data = self::CurlGet($url);
		$data = json_decode($data, true);

		$data['keymatch'] = $data['sha1'] == $sha1;

		return $data;
	}
}