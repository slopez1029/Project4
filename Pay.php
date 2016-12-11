<?php
/**
 * SQL configuration
 */
$mysqli = new mysqli("mysql.eecs.ku.edu", "slopez", "Password123!", "slopez");


/**
 * Checks to see if sql connection is properly configured
 */
if ($mysqli->connect_errno) {
     echo"$mysqli->connect_error)";
    exit();
}

/**
 * Allows for reading of stored session variables, and for ability to store session variables
 */
session_start();
$name = $_POST['selectOption'];
$groupName = $_SESSION['GroupName'];
$groupAdmin = $_SESSION['GroupAdmin'];
$payAmount = $_POST['totalPaid'];
$type = "Payment Confirm Request";
$user = $_SESSION['PayerID'];

$paid = 0;
$payerID = "";
$totalDue = 0;
$dueDate = "";
$amtOwed = 0;
$amtPaid = 0;

 /**
 * This query is for retrieving transaction specific table values from Bills for the PayString column, to be accessed and parsed by the ConfirmPayment php file.
 */
$query = "SELECT * FROM Bills WHERE Name = '$name' AND GroupAdmin = '$groupAdmin'";
if ($result = $mysqli->query($query)) 
    {        
        
        while ($row = $result->fetch_assoc()) 
        {     
            $totalDue = $row['TotalDue'];
            $dueDate = $row['DueDate'];
            $payerID = $row['PayerID'];
	    $paid = $row['TotalPaid'];
        }

       // $result->free();
    }
    
$query = "SELECT * FROM UserBills WHERE Name = '$name' AND PayerID = '$user' AND GroupAdmin = '$groupAdmin'";
if ($result = $mysqli->query($query)) 
    {        
        
        while ($row = $result->fetch_assoc()) 
        {     
			$amtOwed = $row['AmtOwed'];
			$amtPaid = $row['AmtPaid'];
        }

       // $result->free();
    }
	
   /**
   * Checks to see if transaction values were retrieved correctly.
   */
if($payerID != "" && $dueDate != "" )
{
	$content = "$user wants to confirm payment of $$payAmount for the $name bill due on $dueDate";
	/**
	* This payString is referenced by the confirmPayment php file, so that bill table values can be updated 
	*/
	$payString = "billPayer=$user&billName=$name&payAmount=$payAmount&totalDue=$totalDue&totalPaid=$paid&amtOwed=$amtOwed&amtPaid=$amtPaid";
	
	/**
	* This query is for adding a notification to be seen by the bill owner, making sure the right group is being referenced with session variables.
	*/
	$query = "INSERT INTO Notifications (Type,Content,PayerID,GroupName,GroupAdmin,PayString) VALUES ('$type','$content','$payerID','$groupName','$groupAdmin','$payString')";
	if ($result = $mysqli->query($query))
	{
	    echo "The request to confirm payment of $$payAmount for the $name bill due on $dueDate was successfully sent to $payerID!";
	    $result->free();        
	}
}
else
{
	echo "Couldn't send the request to the bill payer!";
}

echo "Total Due: $totalDue"; echo "<br>";
echo "Total Paid: $payAmount"; echo "<br>";
echo "Due Date: $dueDate"; echo "<br>";
echo "Payer ID: $payerID"; echo "<br>";
echo "Group Name: $groupName"; echo "<br>";
echo "Group Admin: $groupAdmin"; echo "<br>";
//$dueDate = strtotime($_POST["dueDate"]);
//$dueDate = date('Y-m-d H:i:s', $dueDate);


/* close connection */

?>
