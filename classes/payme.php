<?php

    /*
     * Copyright (C) 2014 Mies van der Lippe
     *
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     *      http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     */

    /**
     * Class Payme
     */
    class Payme {

        const email = "71989@ict-lab.nl";
        const pmid  = "86nmb6fonm";
        const pmkey = "ikjw6gux6954m3cjgaj5d77rs70ncbey";

        /**
         *
         */
        private function __construct() {
        }

        /**
         * @return mixed
         */
        public static function GetBankList() {

            $jsonurl  = "http://payme.ict-lab.nl/api/banklist/";
            $json     = self::CurlGet($jsonurl);
            $banklist = json_decode($json, true);

            return $banklist;
        }

        /**
         * @param $url
         *
         * @return mixed
         */
        private static function CurlGet($url) {

            $ch      = curl_init();
            $options = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL            => $url
            );

            curl_setopt_array($ch, $options);

            $data = curl_exec($ch);

            curl_close($ch);

            return $data;
        }

        /**
         * @param $url
         *
         * @return mixed
         */
        public static function SpecialUrlDecode($url) {

            $decodedURL  = urldecode($url);
            $replacedURL = str_replace("#", "/", $decodedURL);

            return $replacedURL;
        }

        /**
         * @param $amount
         * @param $bankID
         * @param $purchaseID
         * @param $description
         * @param $returnURL
         * @param $failURL
         *
         * @return mixed
         */
        public static function StartTransaction($amount, $bankID, $purchaseID, $description, $returnURL, $failURL) {

            $amount = preg_replace("/[^0-9]/", "", $amount);

            $returnURL = self::SpecialUrlEncode($returnURL);
            $failURL   = self::SpecialUrlEncode($failURL);

            $verifcationKey = sha1(self::pmid . self::pmkey . $purchaseID . $amount);

            $url = 'http://payme.ict-lab.nl/api/starttrans/' . self::pmid . '/' . self::pmkey . '/' . $amount . '/' . $bankID . '/' . $purchaseID . '/' . urlencode($description) . '/' . $returnURL . '/' . $failURL . '/' . $verifcationKey . '/';

            $data = self::CurlGet($url);
            $data = json_decode($data, true);

            $data['keyMatch'] = $data['sha1'] == $verifcationKey;

            $data['fwdurl'] = urldecode($data['fwdurl']);

            return $data;
        }

        /**
         * @param $url
         *
         * @return string
         */
        public static function SpecialUrlEncode($url) {

            $replacedURL = str_replace("/", "#", $url);
            $encodedURL  = urlencode($replacedURL);

            return $encodedURL;
        }

        /**
         * @param $transactionID
         * @param $sha1
         *
         * @return mixed
         */
        public static function GetTransactionStatus($transactionID, $sha1) {

            $url = 'http://payme.ict-lab.nl/api/statusrequest/';

            $url = $url . $transactionID . '/' . $sha1 . '/';

            $data = self::CurlGet($url);
            $data = json_decode($data, true);

            $data['keymatch'] = $data['sha1'] == $sha1;

            return $data;
        }
    }