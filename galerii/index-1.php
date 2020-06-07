<?php
  $conn = new mysqli("localhost", "ceu", "a", "wordpress");
  
  if ($conn->connect_error) {
    die("ERROR: Unable to connect: " . $conn->connect_error);
  } 

  echo 'Connected to the database.<br>';

  $sql = "SELECT * FROM wp_users, wp_ngg_gallery WHERE wp_users.ID=wp_ngg_gallery.author AND wp_users.ID='3'";
  foreach ($conn->query($sql) as $row) {
  		$gid = $row['gid'];
        echo $row['slug']." - ".$row['user_email'] . "<br>";

     $sql1 = "SELECT * FROM  wp_ngg_pictures WHERE wp_ngg_pictures.galleryid='$gid'";
     foreach ($conn->query($sql1) as $row1) {
        echo "<a href='../../".$row['path']."/".$row1['filename']."'><img src='../../".$row['path']."/thumbs/thumbs_".$row1['filename']."'></a><br>";
    }
  }
  
  $conn->close();
?>