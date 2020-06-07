<?php
//session_start();

if(isset($_SESSION['userID'])){
$id = $_SESSION['userID'];
$ime = $_POST['ime'];
$prezime = $_POST['prezime'];
$adresa = $_POST['adresa'];
$profil_slika = $_POST['profil_slika'];
$profil_slika1 = basename($_FILES["fileToUpload"]["name"]);
$grad = $_POST['grad'];
$postenski_broj = $_POST['postenski_broj'];
$telefon =  $_POST['telefon'];

//echo $profil_slika;
if(!empty($profil_slika1)){
	$target_dir = "post_login/uploads/$id/profil-slika/";
	move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_dir . $_FILES["fileToUpload"]["name"]);
	$profil_slika = basename($_FILES["fileToUpload"]["name"]);
}


  	$id = $_SESSION['userID'];
	
	if(isset($id)==true && $ime==true && $prezime==true){
		
		$STH = $conn -> prepare( "UPDATE user SET first = '$ime', last = '$prezime', adresa = '$adresa', fileURL = '$profil_slika', grad = '$grad', postenski_broj = '$postenski_broj', telefon='$telefon' WHERE id = '$id'" );
		$STH -> execute();
		
		
		echo "Успешно зачувани податоци.";
		header("Location: index.php?page=post_login/dashboard");
	}
	else {
	    echo "Error updating record: " . $conn->error;
	}

 }

?>