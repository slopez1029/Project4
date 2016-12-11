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
$amtPaid = $row["AmtPaid"];
$groupName = $_SESSION['GroupName'];
$gBillNameArray = Array();
$splitCostArray = Array();
$payerIDArray = Array();
$assignedBillArray = Array();
$removedPayer = $_POST['checklist'];

$query = "SELECT * FROM Bills WHERE GroupAdmin = '$groupAdmin'";
if ($result = $mysqli->query($query)) 
{
	while ($row = $result->fetch_assoc()) 
       {
		if($row["PayerID"] == $removedPayer[0])
		{
		   array_push($assignedBillArray,$row["Name"]);
		}
	}
}


if(!empty($_POST['checklist'])) 
{    
	echo "Success! The following people were removed: <br>"; //echo count($cart);
	  
	/**
	* Deletes bills associated with the payer being removed.
	*/
	$query = "DELETE FROM Bills WHERE PayerID = '$removedPayer[0]' AND GroupAdmin = '$groupAdmin' ";
	if ($result = $mysqli->query($query)) {}  
		
	$query = "DELETE FROM UserBills WHERE PayerID = '$removedPayer[0]' AND GroupAdmin = '$groupAdmin' ";
	if ($result = $mysqli->query($query)) {} 

	foreach ($assignedBillArray as $name)
	{ 	
		$query = "DELETE FROM UserBills WHERE Name = '$name' AND GroupAdmin = '$groupAdmin' ";
		if ($result = $mysqli->query($query)){}                                                       
	}
	
        /**
	* Removes payer from group by changing the groupname and groupadmin fields to empty field, not allowing the user to access home navigation.
	*/
	$query = "UPDATE BillPayers SET GroupName= '', GroupAdmin= '' WHERE PayerID= '$removedPayer[0]'"; 
	if ($result = $mysqli->query($query)){}            
	
$groupCount = 0;
$query = "SELECT PayerID FROM BillPayers where GroupAdmin = '$groupAdmin'";
if ($result = $mysqli->query($query)) 
{
    while ($row = $result->fetch_assoc()) 
    {	
	$payerID = $row["PayerID"];
        $groupCount = $groupCount + 1;	
	array_push($payerIDArray,$payerID);		
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
	}
	$result->free();
}

//updating bills of current members with the updated splitCost of removing a member.
$counter = 0;
foreach($gBillNameArray as $gBillName)
{
	$due = $splitCostArray[$counter];

	$query = "UPDATE UserBills SET AmtOwed= '$due' WHERE Name= '$gBillName' AND GroupAdmin = '$groupAdmin'";
	if ($result = $mysqli->query($query)) {}
	
	$counter = $counter + 1;
}
 
//notifying remaining people in group of the member being removed.
$counter = 0;
$type = "PayerRemoved";
$payString = "";
$content = "$removedPayer[0] has left the $groupName group! Personal bills have been updated.";
$payerID = "public";
//foreach($payerIDArray as $payerID)
//{
	$query = "INSERT INTO Notifications (Type,Content,PayerID,GroupName,GroupAdmin,PayString) VALUES ('$type','$content','$payerID','$groupName','$groupAdmin','$payString')";
	if ($result = $mysqli->query($query)){ }
//}

}
else
{
	echo "Nobody was removed since no one was selected.";
} 
	 
?>
