<?php
// code to protect post-login pages

    // check to see whether the user is logged in or not
    if(empty($_SESSION['is_logged_in']))
    {
        // if they are not, redirect them to the login page
		header('Location: ' . PAGE . 'login');
		exit;
    }
?> 


<div class="mt-1">
  <h1>Dashboard</h1>
</div>
<p class="lead">Добродојдовте <?php echo $_SESSION['first']; ?> во заштитениот дел.
<?php
$id = $_SESSION['userID'];

$STH = $conn -> prepare( "SELECT * FROM user WHERE user.id=$id" );
$STH -> execute();
$row = $STH -> fetch();
  
  

	if(isset($_SESSION['userID'])==true){	
  	
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
    $registracija = $row['created'];
	}

	
  
  ?>
<br><br><img src="<?php echo 'post_login/uploads/'.$id.'/profil-slika/'.$profil_slika ?>" width="150">
<br>Кориснички број: <?php echo $_SESSION['userID'] ?>
<br>Емаил адреса: <?php echo $email ?>
<br>Име и Презиме: <?php echo $ime . ' ' . $prezime ?>
<br>Адреса: <?php echo $adresa ?>
<br>Град: <?php echo $grad ?>
<br>Поштенски број: <?php echo $postenski_broj ?>
<br>Телефон: <?php echo $telefon ?>
<br>Регистрација: <?php echo $registracija ?>
</p><a href="index.php?page=post_login/licni_podatoci"><button type="button" class="btn btn-primary">Промени</button></a>

