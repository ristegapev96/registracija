<?php
//session_start();
session_start();
if(isset($_SESSION['userID'])){

	$file = @$_POST['pateka'];
	if (!isset($file))
	  {
	  echo ("Error deleting $file");
	  }
	else
	  {
	  unlink($file);
	  echo ("Deleted $file");
	  }

  }

?> 