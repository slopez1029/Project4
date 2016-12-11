<?php
/**
 * SQL configuration
 */


/**
 * Allows for reading of stored session variables, and for ability to store session variables
 */
session_start();
$payerID = $_SESSION['PayerID'];    

 /**
 * This query is to fetch and display to the front end all user specific notifications. Supported notifications are payment confirm requests so far. Allows front end ability to confirm payment with form submission.
 */
$query = "SELECT * FROM Notifications WHERE PayerID = '$payerID'";
if ($result = $mysqli->query($query)) 
{        
	while ($row = $result->fetch_assoc()) 
	{
		//if($row['GroupAdmin'] == $_SESSION['GroupAdmin'])
		//{
		echo 
		"<tr>
		<td height >{$row['Content']}</td>
		<td><input type='checkbox' name = 'checklist[]' value = '{$row['PayString']}' /></td>             
		</tr>\n"; 
		//}
									
	}

	/* free result set */
	$result->free();
}

?>
