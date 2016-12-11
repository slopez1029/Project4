<?php
/**
 * SQL configuration
 */
$mysqli = new mysqli("mysql.eecs.ku.edu", "slopez", "Password123!", "slopez");

/**
 * Allows for reading of stored session variables, and for ability to store session variables
 */
session_start();
$name = $_POST['name'];
$totalDue = $_POST['totalDue'];
$groupName = $_SESSION['GroupName'];
$groupAdmin = $_SESSION['GroupAdmin'];
$payerID = $_SESSION['PayerID'];
$dueDate = strtotime($_POST["dueDate"]);
$dueDate = date('Y-m-d H:i:s', $dueDate);

$groupArray = Array();
$amtOwedArray = Array();
/**
 * Checks to see if sql connection is properly configured
 */
 if ($mysqli->connect_errno) {
     echo"$mysqli->connect_error)";
    exit();
}

echo "Name: $name <br>"; 
echo "Total Due: $totalDue <br>"; 
echo "Group Name: $groupName <br>"; 
echo "Due Date: $dueDate <br>"; 
echo "Group Admin: $groupAdmin <br>"; 
echo "Payer ID: $payerID <br>"; 

$amtPaid = $row["AmtPaid"];
$alreadyExists = false;

$query = "SELECT Name FROM Bills WHERE GroupAdmin = '$groupAdmin'"; 
if ($result = $mysqli->query($query)) 
{
    while ($row = $result->fetch_assoc()) 
    {	
        if($name == $row["Name"])
        {
            $alreadyExists = true;
        }        
    }		
}
 
if(!$alreadyExists)
{
    /**
    * This query creates a new bill by taking in post form input variables from the front end as well as session variables initialized upon login to create a bill associated with a group.
    */
    $query = "INSERT INTO Bills (Name,TotalDue,TotalPaid,DueDate,PayerID,GroupAdmin) VALUES ('$name','$totalDue','$totalPaid','$dueDate','$payerID','$groupAdmin')";
    if ($result = $mysqli->query($query))
    {
        echo "The $name bill was successfully added to the group $groupName by $payerID!";
        //$result->free();        
    }

    $groupCount = 0;
    $query = "SELECT * FROM BillPayers where GroupAdmin = '$groupAdmin'";
    if ($result = $mysqli->query($query)) 
    {
        while ($row = $result->fetch_assoc()) 
        {	
            array_push($groupArray,$row["PayerID"]);		
            $groupCount = $groupCount + 1;			
        }		
    }

    //notifying the group members of the newly added bill.
    $type = "NewGroupBill";
    $payString = "";
    $id1 = "public";
    $content = "The $name bill has been added to the $groupName group!";

    $query = "INSERT INTO Notifications (Type,Content,PayerID,GroupName,GroupAdmin,PayString) VALUES ('$type','$content','$id1','$groupName','$groupAdmin','$payString')";
    if ($result = $mysqli->query($query)){ }

    $split = $totalDue / $groupCount;	
    $splitCost = round($split,2);
    $type = "NewPersonalBill";
    $payString = "";
    $content = "You owe $$splitCost for the newly added $name bill";
    foreach($groupArray as $id)
    {
        $amtOwed = $splitCost;      	
        if($payerID != $id)
        {
            $query = "INSERT INTO UserBills (Name,AmtOwed,AmtPaid,DueDate,PayerID,GroupAdmin) VALUES ('$name','$amtOwed','$amtPaid','$dueDate','$id','$groupAdmin')";
            if ($result = $mysqli->query($query)){}
	
            $query = "INSERT INTO Notifications (Type,Content,PayerID,GroupName,GroupAdmin,PayString) VALUES ('$type','$content','$id','$groupName','$groupAdmin','$payString')";
            if ($result = $mysqli->query($query)){}
        }
    }	
    echo "<br>";
    echo "Personal billing accounts in the $groupName group have been successfully updated!";
}
else
{
    echo "the bill already exists";
}

$result->free();   

/**
 * Closes the connection.
 */

?>
