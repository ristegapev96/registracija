<?php
// code to protect post-login pages

    // check to see whether the user is logged in or not
    if(empty($_SESSION['is_logged_in']))
    {
        // if they are not, redirect them to the login page
		header('Location: ' . PAGE . 'login');
		exit;
    }



  $id = $_SESSION['userID'];

  $STH = $conn -> prepare( "SELECT * FROM user WHERE user.id=$id" );
  $STH -> execute();
  $row = $STH -> fetch();
  
  if(isset($id)){

  	$target_dir =  __DIR__. "/uploads/" .  $_SESSION['userID'] . "/profil-slika/" ;
  	if (!is_dir($target_dir)) {
	    mkdir($target_dir, 0777, true);
	  }
  	
  	//$directory  = "../post_login/uploads/".$row['id'].'/';   
  	$ime = $row['first'];
  	$prezime = $row['last'];
  	$ime_prezime = $row['first'].' '.$row['last'];
  	$email = $row['email'];
  	$adresa = $row['adresa'];
  	$grad = $row['grad'];
  	$postenski_broj = $row['postenski_broj'];
  	$drzava = $row['drzava'];
  	$telefon = $row['telefon'];
  	$profil_slika = $row['fileURL'];
	}
?>
<div class="mt-1">
  <h1>Лични податоци</h1>
</div>
<p class="lead">
	Кориснички број: <?php echo $id; ?><br>

	Е-маил: <?php echo $email; ?>
</p>
<form action="index.php?page=post_login/azuriraj_podatoci" method="post" enctype="multipart/form-data">
<p class="lead">
	<img src="<?php echo 'post_login/uploads/'.$id.'/profil-slika/'.$profil_slika ?>" width="150">
	<input type="hidden" name="profil_slika" value="<?php echo $profil_slika ?>">

    <input type="file" name="fileToUpload" id="fileToUpload" style="font-size: 15px;"><br><br>

	Име и Презиме: <br><input type="" name="ime" value="<?php echo $ime; ?>" placeholder="Име"> <input type="" name="prezime" value="<?php echo $prezime; ?>" placeholder="Презиме"><br><br>

	Адреса: <br><input type="text" name="adresa" value="<?php echo $adresa; ?>" placeholder="Адреса"><br><br>

	Град: <br><input type="text" name="grad" value="<?php echo $grad; ?>" placeholder="Град"><br><br>

	Поштенски број: <br><input type="text" name="postenski_broj" value="<?php echo $postenski_broj; ?>" placeholder="2000"><br><br>

	Телефон: <br><input type="text" name="telefon" value="<?php echo $telefon; ?>" placeholder="Телефон"><br><br>
</p>
<input type="submit" name="" value="Зачувај" class="btn btn-primary">

</form>