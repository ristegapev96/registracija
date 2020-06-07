<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<title>Simple Lightbox - Responsive touch friendly Image lightbox</title>
	<link href='https://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
	<link href='dist/simplelightbox.min.css' rel='stylesheet' type='text/css'>
	<link href='demo.css' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="container">
<?php
  echo '<h1></hi>';
  session_start();
  $id = "";
  $ime_prezime = "";

  $conn = new mysqli("localhost", "ceu", "a", "registracija");
  $conn->set_charset("utf8");
  if ($conn->connect_error) {
    die("ERROR: Unable to connect: " . $conn->connect_error);
  }

  if(isset($_GET['id'])){
  	$id = $_GET['id'];
  	$sql = "SELECT * FROM user WHERE user.id=$id";
  } 
  else{
  	$sql = "SELECT * FROM user WHERE user.id=user.id";
  }
  
  foreach ($conn->query($sql) as $row) {
  	
  
  	$directory  = "../post_login/uploads/".$row['id'].'/';   

  	$ime_prezime = $row['first'].' '.$row['last'];
  
  
  	  
    if(isset($_GET['id'])){ // prikazi samo sliki dokolku imame id

    	echo '<h1 class="align-center">'.$ime_prezime.'</h1>';
        echo '<div class="gallery">';
    	foreach (glob($directory . "*.{jpg,jpeg,png,gif}", GLOB_BRACE) as $filename) {
         	echo "<a href='".$filename."'><img src='".$filename."' alt='' title=''></a>";
    	}
    	//echo '<div style="display: inline-block;"><h3 class="align-center"><a  style="color: black; text-decoration: none;" href="."><< назад</a></h3></div>';
    }
    else {echo '<h1 class="align-center"><a  style="color: black; text-decoration: none;" href="?id='.$row['id'].'">'.$ime_prezime.'</a></h1>';}
    
    	echo '<div class="clear"></div>';
  }
  
 
?>


<br><br>
			<span style="display: block; font-style: italic; font-size: small; position: fixed; right: 0px; bottom: 0px; display: inline-block; border-top: 1px solid #efefef; padding: 2px;">» Spasov S. «</span><br/>
	</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="dist/simple-lightbox.js"></script>
<script>
	$(function(){
		var $gallery = $('.gallery a').simpleLightbox();

		$gallery.on('show.simplelightbox', function(){
			console.log('Requested for showing');
		})
		.on('shown.simplelightbox', function(){
			console.log('Shown');
		})
		.on('close.simplelightbox', function(){
			console.log('Requested for closing');
		})
		.on('closed.simplelightbox', function(){
			console.log('Closed');
		})
		.on('change.simplelightbox', function(){
			console.log('Requested for change');
		})
		.on('next.simplelightbox', function(){
			console.log('Requested for next');
		})
		.on('prev.simplelightbox', function(){
			console.log('Requested for prev');
		})
		.on('nextImageLoaded.simplelightbox', function(){
			console.log('Next image loaded');
		})
		.on('prevImageLoaded.simplelightbox', function(){
			console.log('Prev image loaded');
		})
		.on('changed.simplelightbox', function(){
			console.log('Image changed');
		})
		.on('nextDone.simplelightbox', function(){
			console.log('Image changed to next');
		})
		.on('prevDone.simplelightbox', function(){
			console.log('Image changed to prev');
		})
		.on('error.simplelightbox', function(e){
			console.log('No image found, go to the next/prev');
			console.log(e);
		});
	});
</script>
</body>
</html>
