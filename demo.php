<?php

require_once('/classes/payme.php');

/*
   Get list of banks available to finish a transaction.
   Returns : 
      Array with banks in it
*/

$banklist = PayMe::GetBankList();

/*
   Start a transaction
   Expects : 
    amount in cents          (INT)       You can leave in other characters. Anything but digits are filtered out.
    Bank ID                  (INT)       ID of the bank you want to finish the transaction with. 
    Your transaction ID      (INT 16)
    Transaction description  (STRING 32) limited to chars a-z, A-Z, 0-9 and -) 'Test der tests'
    ReturnURL                (STRING)    A URL to call when transaction is finished. NOT encoded
    FailedURL                (STRING)    URL to call when transaction fails. NOT encoded

   Returns : 
    array(
      transid    str     payme transaction ID (save this)
      fwdurl     str     a url to redirect your client to
      sha1       str     same key you sent to the API 
      keyMatch   bool    wether sent key and received key match. 
    )   

*/

$returnURL     = 'http://yourdomain.com/succes';
$failURL       = 'http://yourdomain.com/system/transfail';

$amount        = 5000; //You can leave any characters, this is filtered by the class.
$bankID        = 1;
$transid       = 132;
$description   = 'This is a description.';

$StartData     = PayMe::StartTransaction($amount, $bankID, $transid, $description, $returnURL, $failURL);

if($StartData['keyMatch']){
   
   /*
      Transaction started succesfully
   */

   /*
      Redirect your user to this URL, either via JS, Headers or a simple HREF.
   */
   $redirectTo  = $StartData['fwdurl'];

   /*
      Save these to check back on the transaction.
   */

   $transid     = $StartData['transid'];
   $hash        = $StartData['sha1'];

}

/*
   Check back on a transaction.
   Expects : 
    Transid          (STRING)  The Payme transaction ID, not yours.
    Transaction hash (STRING)  The transaction hash. 
   returns :
     transid         (STR 16)  l7pwTgll
     timestamp       (TIME)    1417085359
     amount          (INT)     1000
     bankid          (INT)     1
     status          (STRING)  pending
     sha1            (STRING)  2a37726251599c6ad50ca0aec02e09e02bd3c3f4
     keymatch        (BOOL)    1
*/

$hash    = $hash;      //I'll just recycle the transaction we just started
$transid = $transid;   //I'll just recycle the transaction we just started

$status  = PayMe::GetTransactionStatus($transid, $hash);