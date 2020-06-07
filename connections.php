<?php

	$datasource = 'MySQL' ;


if($datasource === 'MySQL'){
	$host = "localhost";
	$usn = "user";
	$pwd = "a";
	$db = "registracija";
	
	$tUser = 'user';
	$tReset = 'reset';
	$tLog = 'log';
	$tFile = 'file';
    $tUsertypes = 'usertypes';


	// for PDO connection to database
	$pdo = 'mysql:host='.$host.';dbname='.$db .';charset=utf8mb4';


	// Check connection
	try {
	// establish connection
		$conn = new PDO($pdo, $usn, $pwd,
		array(
		// set the PDO error mode to ERRMODE_WARNING to show MySQL errors:
			PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
			)
		);

	// report success
	//	echo 'Successfully connected to database.<br>';

	} catch (PDOException $e) {
	// report error
		echo '<pre>';
		echo 'Failed to connect to the MySQL database: ' . $e->getMessage();
		echo '</pre>';
		file_put_contents('/pdo_errors/pdo_errors.txt', $e->getMessage()."\n", FILE_APPEND);
		exit;
	}

}