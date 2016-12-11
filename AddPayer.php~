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

$payerID = $_POST['payerID'];
$password = "notset";
$groupName = $_SESSION['GroupName'];
$groupAdmin = $_SESSION['GroupAdmin'];

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
    $result->free();        
}


/**
 * Closes the connection
 */
$mysqli->close();
?>
