<?php
// code to protect post-login pages

    // check to see whether the user is logged in or not
    if(empty($_SESSION['is_logged_in']))
    {
        // if they are not, redirect them to the login page
		header('Location: ' . PAGE . 'login');
		exit;
    }
$query = "SELECT * from user";
$data = $conn->query($query);
echo '<table width="20%" border="1" cellpadding="1" cellspacing="1">
    <tr>
    <th>Ime</th>
    <th>Prezime</th>
    <tr>';
    
foreach($data as $row)
{
    echo '<tr>
            <td>'.$row["first"].'</td>
            <td>'.$row["last"].'</td>
            </tr>';
}
?> 
