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

$amtPaid = $row["AmtPaid"];
$payerID = $_POST['payerID'];
$password = "notset";
$groupName = $_SESSION['GroupName'];
$groupAdmin = $_SESSION['GroupAdmin'];
$gBillNameArray = Array();
$splitCostArray = Array();
$dueDateArray = Array();
$payerIDArray = Array();


/**
 * This query is for checking to see if the user already has an account. If not, they can't be added to the group.
 */
$query = "SELECT * FROM BillPayers";
$userExists = false;
if ($result = $mysqli->query($query)) {

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) 
    {
	
		if($row["PayerID"] == $payerID)
		{
		  $userExists = true;
		  $password = $row["Password"];
		}		

    }
			
    /* free result set */
    $result->free();
}

/**
 * This query is for switching the group that the existing user is currently in, to the one of the user that is adding them, by updating the groupName and groupAdmin fields.
 */
$query = "UPDATE BillPayers SET GroupName= '$groupName', GroupAdmin= '$groupAdmin' WHERE PayerID= '$payerID'"; 
if(!$userExists)
{
	echo "No account found with given username, couldn't add anyone to the group.";
}
else if ($result = $mysqli->query($query))
{
 	echo "The bill payer $payerID was added successfully to the group $groupName!";
    //$result->free();        
}
if($userExists){
$groupCount = 0;
$query = "SELECT PayerID FROM BillPayers where GroupAdmin = '$groupAdmin'";
if ($result = $mysqli->query($query)) 
{
    while ($row = $result->fetch_assoc()) 
    {	
	$user = $row["PayerID"];
        $groupCount = $groupCount + 1;	
	array_push($payerIDArray,$user);		
    }		
}

echo"<br>";
echo "GroupCount: "; echo "$groupCount";
$splitCost = 0;
$query = "SELECT * FROM Bills WHERE GroupAdmin = '$groupAdmin'";
if ($result = $mysqli->query($query)) 
{
	while ($row = $result->fetch_assoc()) 
    {
		$totalDue = $row["TotalDue"];
		$split = $totalDue / $groupCount;	
		$splitCost = round($split,2);
		array_push($gBillNameArray,$row["Name"]);
		array_push($splitCostArray,$splitCost);
		array_push($dueDateArray,$row["DueDate"]);
	}
	$result->free();
}

//adding user bills into newly added member and updating bills of current members
$counter = 0;
foreach($gBillNameArray as $gBillName)
{
	$due = $splitCostArray[$counter];
	$dueDate = $dueDateArray[$counter];
	$query = "INSERT INTO UserBills (Name,AmtOwed,AmtPaid,DueDate,PayerID,GroupAdmin) VALUES ('$gBillName','$due','$amtPaid','$dueDate','$payerID','$groupAdmin')";
	if($result = $mysqli->query($query)) {}

	$query = "UPDATE UserBills SET AmtOwed= '$due' WHERE Name= '$gBillName' AND GroupAdmin = '$groupAdmin'";
	if ($result = $mysqli->query($query)) {}
	
	$counter = $counter + 1;
}

//notifying others in the group of the newly added member.
$counter = 0;
$type = "PayerAdded";
$payString = "";
$content = "$payerID has joined the $groupName group! Personal bills have been updated.";
$payer = "public";
//foreach($payerIDArray as $payer)
//{
	//if($payerID != $payer)
	//{
		$query = "INSERT INTO Notifications (Type,Content,PayerID,GroupName,GroupAdmin,PayString) VALUES ('$type','$content','$payer','$groupName','$groupAdmin','$payString')";
		if ($result = $mysqli->query($query)){ }
	//}
//}

}
?>
