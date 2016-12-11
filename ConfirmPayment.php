<?php
    
/**
* SQL configuration
*/
$mysqli = new mysqli("mysql.eecs.ku.edu", "slopez", "Password123!", "slopez");

/**
 * Checks to see if sql connection is properly configured
 */
if ($mysqli->connect_errno) 
{
	printf("Connect failed: %s\n", $mysqli->connect_error);
	exit();
}

/**
 * Allows for reading of stored session variables, and for ability to store session variables
 */
session_start();
$groupAdmin = $_SESSION['GroupAdmin'];
$groupName = $_SESSION['GroupName'];
$payerID = $_SESSION['PayerID'];	

if(!empty($_POST['checklist'])) 
{    
	echo "Payment Confirmed! The group's billing account and the bill payer's personal billing account have been updated. <br>"; //echo count($cart);
	
	/**
	* For each checked payment notification, generate a query to update the totalPaid and totalDue columns with calculated values from the payment amount and current 'Bills' table values .
	*/
	foreach ($_POST['checklist'] as $check)
	{
	   // echo $check."<br />";	
	   /**
		* Allows to read in the PayString value that's stored in check, which is a parseable string that contains transaction information.
		*/
		parse_str($check);
		echo "$totalDue"; echo "<br>";echo "$payAmount"; echo "<br>";echo "$totalPaid";
		$difference = $totalDue; 		
		$sum = $totalPaid + $payAmount;
		$nameofbill = $billName;
		
		$AmtOwed = $amtOwed;
		$AmtPaid = $amtPaid + $payAmount;
		$BillPayer = $billPayer;
		$type = "PaymentConfirmed";
		$payString = "";
		$content = "$payerID has confirmed your payment of $$payAmount for the $nameofbill bill!";
		
		//echo "$difference"; echo "$sum";
		/**
		* This query updates the Bills table with calculated values involving existing table values and 
		*/
		$query = "UPDATE Bills SET TotalDue = '$difference', TotalPaid= '$sum' WHERE Name= '$nameofbill' AND GroupAdmin = '$groupAdmin' "; 
		if ($result = $mysqli->query($query)) { }            
		
		$query = "UPDATE UserBills SET AmtOwed = '$AmtOwed', AmtPaid= '$AmtPaid' WHERE PayerID = '$BillPayer' AND GroupAdmin = '$groupAdmin' "; 
		if ($result = $mysqli->query($query)) {  } 
	
		$query = "INSERT INTO Notifications (Type,Content,PayerID,GroupName,GroupAdmin,PayString) VALUES ('$type','$content','$BillPayer','$groupName','$groupAdmin','$payString')";
		if ($result = $mysqli->query($query)){ }
	}
	
	header("Location: Bills.html");
}
else
{
	echo "Nothing was confirmed since a notification was not selected.";
	header("Location: Bills.html");
}    
	 
?>
