<?php

/**
 * SQL configuration
 */


	/**
	* Allows for reading of stored session variables, and for ability to store session variables
	*/
    session_start();
    
	/**
	* This query is retrieving all the information from the Bills table, to be displayed in the front end with an option to remove a bill with each row.
	*/

    $query = "SELECT * FROM Bills";
    if ($result = $mysqli->query($query)) 
    {        
        $payString = "billPayer=$user&billName=$name&payAmount=$payAmount&totalDue=$totalDue&totalPaid=$paid&amtOwed=$amtOwed&amtPaid=$amtPaid";
        /* fetch associative array */
        while ($row = $result->fetch_assoc()) 
        {
            if($row['GroupAdmin'] == $_SESSION['GroupAdmin'])
            {
            echo 
            "<tr>
              <td>{$row['Name']}</td>
              <td>$ {$row['TotalDue']}</td>
              <td>$ {$row['TotalPaid']}</td>
              <td>{$row['DueDate']}</td>
              <td>{$row['PayerID']}</td>
              <td><input type='checkbox' name = 'checklist[]' value = '{$row['Name']}' /></td>
            </tr>\n"; 
            }
                                        
        }

        /* free result set */
        $result->free();
    }
?>
