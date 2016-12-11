<?php
    
/**
* sql configuration
*/
$mysqli = new mysqli("mysql.eecs.ku.edu", "slopez", "Password123!", "slopez");

/**
*	Check to see if SQL server is properly connected
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
$groupArray = Array();
$amtOwedArray = Array();

$groupCount = 0;
$query = "SELECT * FROM BillPayers where GroupAdmin = '$groupAdmin'";
if ($result = $mysqli->query($query)) 
{
    while ($row = $result->fetch_assoc()) 
    {	
        array_push($groupArray,$row["PayerID"]);		
        array_push($amtOwedArray,$row["AmtOwed"]);
        $groupCount = $groupCount + 1;			
    }		
}

if(!empty($_POST['checklist'])) 
{
    $totalDue = 0;
    foreach ($_POST['checklist'] as $check)
	{
        $query = "SELECT * FROM Bills WHERE Name = '$check' AND GroupAdmin = '$groupAdmin'";
        if ($result = $mysqli->query($query)) 
        {   
            while ($row = $result->fetch_assoc()) 
            {	
                //array_push($totalDueArray,$row["TotalDue"]);	
                $totalDue = $totalDue + $row["TotalDue"];
            }		
        } 
    }
    
    echo "Success! The billpayers' accounts in the group have been updated. <br>";
    $split = $totalDue / $groupCount;	
    $splitCost = round($split,2);
    $counter = 0;	
    foreach($groupArray as $id)
    {
        $amtOwed = $amtOwedArray[$counter];        	
        $AmtOwed = $amtOwed - $splitCost;

        $counter = $counter + 1;
    }
    
	echo "Success! The following bills were removed: <br>";

	foreach ($_POST['checklist'] as $check)
	{
		echo $check."<br />"; 
		/**
		* Deletes the bill based on the name and the associated group.
		*/
		$query = "DELETE FROM Bills WHERE Name = '$check' AND GroupAdmin = '$groupAdmin' ";

		if ($result = $mysqli->query($query)) { }		
		$query = "DELETE FROM UserBills WHERE Name = '$check' AND GroupAdmin = '$groupAdmin' ";
		if ($result = $mysqli->query($query)) 
		{                                                                
			$result->free();
		}
	}
}
else

{
	echo "No bill was removed since nothing was selected.";
}
		
?>
