<?php

/**
 * Allows for reading of stored session variables, and for ability to store session variables
 */
session_start();

$groupAdmin = $_SESSION['GroupAdmin'];
/**
 * Retrieves and displays all the bill payers in the group, along with the ability to remove a payer.
 */
$query = "SELECT * FROM BillPayers WHERE GroupAdmin = '$groupAdmin'";
if ($result = $mysqli->query($query)) 
{        
	while ($row = $result->fetch_assoc()) 
	{
		//if($row['GroupAdmin'] == $_SESSION['GroupAdmin'])
		//{
		echo 
		"<tr>
		<td>{$row['PayerID']}</td>
		<td><input type='checkbox' name = 'checklist[]' value = '{$row['PayerID']}' /></td>             
		</tr>\n"; 
		//}
									
	}

	$result->free();
}

?>
