<?php
/**
 * Created by PhpStorm.
 * User: aymanbitar
 * Date: 7/17/16
 * Time: 3:11 PM
 */

namespace App\Helpers;


use Request;

class PaymentHelper {

	public $SECURE_SECRET = "B6EE3A6B89EA2A2DF0F1EF1BAB4D5CF6";
	public $appendAmp = 0;
	public $vpcURL = "";
	public $newHash = "";
	//Supplied by Payment Gateway
	public $access_code = "2FFBE5AF";

	//The Id of the transaction in application database
	public $merchTxnRef = 4456;

	//Supplied by Payment Gateway
	public $merchant_id = "TEST895901";

	//The Id of the transaction in application database
	public $orderInfo = 0;

	public $amount = 100;

	public $return_url = "http://ssignature.tedmob.com/api/profile/pay_membership/return?action=py";

	public function submit() {
		$parameters = self::build_parameter();
		ksort($parameters);
		$md5HashData = $this->SECURE_SECRET;

		foreach($parameters as $key => $value)
		{
			// create the md5 input and URL leaving out any fields that have no value
			if (strlen($value) > 0 && ($key == 'accessCode' || $key == 'merchTxnRef' || $key == 'merchant' || $key == 'orderInfo' || $key == 'amount' || $key == 'returnURL')) {
//				print 'Key: '.$key.'  Value: '.$value."<br>";
				// this ensures the first paramter of the URL is preceded by the '?' char
				if ($this->appendAmp == 0)
				{
					$this->vpcURL .= urlencode($key) . '=' . urlencode($value);
					$this->appendAmp = 1;
				} else {
					$this->vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
				}
				$md5HashData .= $value;
			}
		}

//		print $this->vpcURL . PHP_EOL;

		$this->newHash .= $this->vpcURL."&vpc_SecureHash=" . strtoupper(md5($md5HashData));

		return redirect("https://gw1.audicards.com/TPGWeb/payment/prepayment.action?$this->newHash");
	}

	public function redirect(Request $request) {
		$parameters = $request->all();
		//get secure hash value of merchant
		//get the secure hash sent from payment client
		$vpc_Txn_Secure_Hash = addslashes($parameters["vpc_SecureHash"]);
		unset($parameters["vpc_SecureHash"]);
		ksort($parameters);

		// set a flag to indicate if hash has been validated
		$errorExists = false;

		if (strlen($this->SECURE_SECRET) > 0 && addslashes($_GET["vpc_TxnResponseCode"]) != "7" && addslashes($_GET["vpc_TxnResponseCode"]) != "No Value Returned")
		{
			//creat an md5 variable to be compared with the passed transaction secure hash to check if url has been tampered with or not
			$md5HashData = $this->SECURE_SECRET;

			//creat an md5 variable to be compared with the passed transaction secure hash to check if url has been tampered with or not
			$md5HashData_2 = $this->SECURE_SECRET;

			// sort all the incoming vpc response fields and leave out any with no value
			foreach($_GET as $key => $value)
			{
				if ($key != "vpc_SecureHash" && strlen($value) > 0 && $key != 'action' )
				{

					$md5HashData_2 .= str_replace(" ",'+',$value);
					$md5HashData .= $value;

				}
			}

			//if transaction secure hash is the same as the md5 variable created
			if ((strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData)) || strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData_2))))
			{
				$hashValidated = "<b>CORRECT</b>";
			}
			else
			{
				$hashValidated = "<b>INVALID HASH</b>";
				$errorExists = true;
			}
		} else {
			$hashValidated = "Not Calculated - No 'SECURE_SECRET' present.";
		}

		//the the fields passed from the url to be displayed
		$amount          = self::null2unknown(addslashes($_GET["amount"])/100);
		$locale          = self::null2unknown(addslashes($_GET["vpc_Locale"]));
		$batchNo         = self::null2unknown(addslashes($_GET["vpc_BatchNo"]));
		$command         = self::null2unknown(addslashes($_GET["vpc_Command"]));
		$message         = self::null2unknown(addslashes($_GET["vpc_Message"]));
		$version         = self::null2unknown(addslashes($_GET["vpc_Version"]));
		$cardType        = self::null2unknown(addslashes($_GET["vpc_Card"]));
		$orderInfo       = self::null2unknown(addslashes($_GET["orderInfo"]));
		$receiptNo       = self::null2unknown(addslashes($_GET["vpc_ReceiptNo"]));
		$merchantID      = self::null2unknown(addslashes($_GET["merchant"]));
		$authorizeID     = self::null2unknown(addslashes($_GET["vpc_AuthorizeId"]));
		$merchTxnRef     = self::null2unknown(addslashes($_GET["merchTxnRef"]));
		$transactionNo   = self::null2unknown(addslashes($_GET["vpc_TransactionNo"]));
		$acqResponseCode = self::null2unknown(addslashes($_GET["vpc_AcqResponseCode"]));
		$txnResponseCode = self::null2unknown(addslashes($_GET["vpc_TxnResponseCode"]));

		// Show 'Error' in title if an error condition
		$errorTxt = "";

		// Show this page as an error page if vpc_TxnResponseCode equals '7'
		if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned" || $errorExists) {
			$errorTxt = "Error ";
		} ?>
		<!-- end branding table -->
		<!-- End Branding Table -->
		<table width="85%" align="center" cellpadding="5" border="0">
			<tr>
				<td align="right"><b>Hash Validity:</b></td>
				<td class="errorMsg"><?php echo $hashValidated?></td>
			</tr>

			<tr>
				<td align="right"><b>Merchant Transaction Reference: </b></td>
				<td><?php echo $merchTxnRef?></td>
			</tr>
			<tr>
				<td align="right"><b>Merchant ID: </b></td>
				<td><?php echo $merchantID?></td>
			</tr>
			<tr>
				<td align="right"><b>Order Information: </b></td>
				<td><? echo $orderInfo?></td>
			</tr>
			<tr>
				<td align="right"><b>Purchase Amount: </b></td>
				<td><? echo $amount?></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<hr />
				</td>
			</tr>

			<tr>
				<td colspan="2" align="center">
					Fields above are the request values returned.<br>
					Fields below are the response fields for a Standard Transaction.<br>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<hr />
				</td>
			</tr>
			<tr>
				<td align="right"><b>VPC Transaction Response Code: </b></td>
				<td><? echo $txnResponseCode?></td>
			</tr>
			<tr>
				<td align="right"><b>Transaction Response Code Description:</b></td>
				<td class="errorMsg"><?= self::getResponseDescription($txnResponseCode)?></td>
			</tr>
			<tr>
				<td align="right"><b>Message: </b></td>
				<td><? echo $message?></td>
			</tr>
			<?php
			// only display the following fields if not an error condition
			if ($txnResponseCode != "7" && $txnResponseCode != "No Value Returned") { ?>
				<tr>
					<td align="right"><b>Receipt Number: </b></td>
					<td><?php echo $receiptNo?></td>
				</tr>
				<tr>
					<td align="right"><b>Transaction Number: </b></td>
					<td><? echo $transactionNo?></td>
				</tr>
				<tr>
					<td align="right"><b>Acquirer Response Code: </b></td>
					<td><? echo $acqResponseCode?></td>
				</tr>
				<tr>
					<td align="right"><b>Bank Authorization ID: </b></td>
					<td><? echo $authorizeID?></td>
				</tr>
				<tr>
					<td align="right"><b>Batch Number: </b></td>
					<td><? echo $batchNo?></td>
				</tr>
				<tr>
					<td align="right"><b>Card Type: </b></td>
					<td><? echo $cardType?></td>
				</tr>
				<tr>
					<td colspan="2"><HR /></td>
				</tr>
			<?php } ?>

		</table> <?php

	}

	function getResponseDescription($responseCode)
	{
		switch ($responseCode) {
			case "0" : $result = "Transaction Successful"; break;
			case "?" : $result = "Transaction status is unknown"; break;
			case "1" : $result = "Unknown Error"; break;
			case "2" : $result = "Bank Declined Transaction"; break;
			case "3" : $result = "No Reply from Bank"; break;
			case "4" : $result = "Expired Card"; break;
			case "5" : $result = "Insufficient funds"; break;
			case "6" : $result = "Error Communicating with Bank"; break;
			case "7" : $result = "Payment Server System Error"; break;
			case "8" : $result = "Transaction Type Not Supported"; break;
			case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
			case "A" : $result = "Transaction Aborted"; break;
			case "C" : $result = "Transaction Cancelled"; break;
			case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
			case "E" : $result = "Invalid Credit Card"; break;
			case "F" : $result = "3D Secure Authentication failed"; break;
			case "I" : $result = "Card Security Code verification failed"; break;
			case "G" : $result = "Invalid Merchant"; break;
			case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
			case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
			case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
			case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
			case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
			case "T" : $result = "Address Verification Failed"; break;
			case "U" : $result = "Card Security Code Failed"; break;
			case "V" : $result = "Address Verification and Card Security Code Failed"; break;
			case "X" : $result = "Credit Card Blocked"; break;
			case "Y" : $result = "Invalid URL"; break;
			case "B" : $result = "Transaction was not completed"; break;
			case "M" : $result = "Please enter all required fields"; break;
			case "J" : $result = "Transaction already in use"; break;
			case "BL" : $result = "Card Bin Limit Reached"; break;
			case "CL" : $result = "Card Limit Reached"; break;
			case "LM" : $result = "Merchant Amount Limit Reached"; break;
			case "Q" : $result = "IP Blocked"; break;
			case "Z" : $result = "Bin Blocked"; break;

			default  : $result = "Unable to be determined";
		}
		return $result;
	}

	function null2unknown($data)
	{
		if ($data == "")
			return "No Value Returned";
		else
			return $data;
	}

	function build_parameter() {
//		[accessCode] => 2FFBE5AF [merchTxnRef] => 1468921471 [merchant] => TEST895901 [orderInfo] => 1468921471 [amount] => 500 [returnURL] => http://localhost/~aymanbitar/tests/bank-audi/paymentTestForm.php?action=py [SubButL] => Pay Now!
		return array(
			'accessCode' => $this->access_code,
			'merchTxnRef' => $this->merchTxnRef,
			'merchant' => $this->merchant_id,
			'orderInfo' => $this->orderInfo,
			'amount' => $this->amount,
			'returnURL' => $this->return_url
		);
	}

	/**
	 * @return string
	 */
	public function getMerchTxnRef() {
		return $this->merchTxnRef;
	}

	/**
	 * @param string $merchTxnRef
	 */
	public function setMerchTxnRef( $merchTxnRef ) {
		$this->merchTxnRef = $merchTxnRef;
	}

	/**
	 * @return string
	 */
	public function getOrderInfo() {
		return $this->orderInfo;
	}

	/**
	 * @param string $orderInfo
	 */
	public function setOrderInfo( $orderInfo ) {
		$this->orderInfo = $orderInfo;
	}

	/**
	 * @return int
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @param int $amount
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;
	}

	/**
	 * @return string
	 */
	public function getReturnUrl() {
		return $this->return_url;
	}

	/**
	 * @param string $return_url
	 */
	public function setReturnUrl( $return_url ) {
		$this->return_url = $return_url;
	}
}