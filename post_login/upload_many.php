<?php
// code to protect post-login pages

    // check to see whether the user is logged in or not
    if(empty($_SESSION['is_logged_in']))
    {
        // if they are not, redirect them to the login page
		header('Location: ' . PAGE . 'login');
		exit;
    }

$continue = null ;
$message = array() ;
$uploadOK = 0;
$deleteAfterUpload = 0 ; // to delete file from dirctory after uploading, change to 1


if ( isset($_POST['UploadForm']) && count($_FILES['fileToUpload']['tmp_name']) == 0 ) {
	$message[] = "Select a file to upload before clicking upload!";
} else {
	$continue = 1;
}

// CHECK THE FILE TYPE
if ( isset($_POST['UploadForm']) && $continue && count($_FILES['fileToUpload']['tmp_name']) > 0 ) {

$valid_formats = array("pdf", "jpeg", "jpg", "png", "gif",);
// $valid_formats = array("rar","zip","7z","pdf","xlsx","xls","docx","doc", "ppt", "txt", "jpeg", "jpg", "png", "gif",);
$valid_formats_server = array(
	"image/jpeg",
	"image/png",
	"image/gif",
	"application/pdf",

//prevent uploading of wrong file types 
foreach ($_FILES['fileToUpload']['type'] as $t => $Type) {
	if(!in_array($_FILES['fileToUpload']['type'][$t], $valid_formats_server)){
		$message[] = $_FILES['fileToUpload']['name'][$t]  . " is the wrong file type (" . $Type. ").";
		$message[] = "Please make sure all uploads are of the correct file type (jpeg, jpg, png, gif, pdf)";
		$continue = 0 ;
	}
}
// ***************************************************************
}

// CREATE THE UPLOAD FOLDER, CHECK THE FILE
if( isset($_POST['UploadForm']) && $continue == 1 ) {

$target_dir =  __DIR__. "/uploads/" .  $_SESSION['userID'] . "/" ;

// create upload directory if it doesn't already exist
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$max_file_size = 5*1024*1024; //5MB
$path = "uploads/"; // Upload directory
$count = 0; // nbr of successfully uploaded files
$filenames = ''; //names of successfully uploaded files

$files = count($_FILES['fileToUpload']['name']) ; // number of files to upload


// **************************************************************		
// UPLOAD EACH FILE
// loop thru all files
foreach ($_FILES['fileToUpload']['name'] as $f => $name) {
	if ($_FILES['fileToUpload']['error'][$f] == 4) {
		$message[] = $_FILES['fileToUpload']['error'][$f];
		// Skip file if any error found, go to next file in loop
			continue; 
	}
	if ($_FILES['fileToUpload']['size'][$f] > $max_file_size) {
		$message[] = "$name is too large!";
		// Skip large files, go to next file in loop
			continue; 
	}
	elseif(!in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats)){
		$message[] = "$name is not a valid format";
		// Skip invalid file formats, go to next file in loop
			continue;
	}
	else{ // No error found! Move uploaded files
   
	// rename file to remove unsafe characters
	$rename = preg_replace( '`[^a-z0-9-_.]`i','_',basename($name) ); 

	//Get the temp file path
	$tmpFilePath = $_FILES['fileToUpload']['tmp_name'][$f];
  
	// from the form input, file to be uploaded
	$target_file = $target_dir . $rename ;  
  
	$fileURL =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/uploads/" .  $_SESSION['userID'] . "/" .  $rename;
  
	$fileURL = str_replace("index.php?page=","",$fileURL);
  

   // UPLOAD THE FILE
   // if the destination file already exists, it will be overwritten
	if (move_uploaded_file($tmpFilePath, $target_file)) {
			$count++; // counting successful uploads
			$filenames .= ', ' . $rename ;
		  
			$message[] = "The file ". $rename. " has been uploaded.";
		  
		// capture urls for each uploaded file
			$links[] = $fileURL;

	   // ***********************************************
	   // CREATE DATABASE RECORDS

	   // CREATE THE MYSQL DATABASE RECORD
			if($datasource == 'MySQL'){
	   // MySQL *****************************************************
	   // Create the MySQL File records
		 $sql = "INSERT INTO " . $tFile . " (fileURL, id_user, description) VALUES (:fileURL, :userID, :description)";
			  $query = $conn->prepare($sql);
			  $query->bindValue(':fileURL', $fileURL);
			  $query->bindValue(':userID', $_SESSION['userID']);
			  $query->bindValue(':description', $_POST['description'][$f]);
			  $query->execute();

			  $errorInfo = $query->errorInfo();

			  if($errorInfo[0] != 0){
				   echo '<p>MySQL Insert Error: ' . $errorInfo[2] . '</p>'; 
				   exit;
			  }	
	   } 

	   // CREATE THE FILEMAKER DATABASE RECORD
			if ($datasource == 'FileMaker') {
	   // FILEMAKER *****************************************************
	   // use a FileMaker script to create the File records instead of PHP
	   // the script uses the "Insert from URL" step to insert the image into the container from the fileURL field

		  $parameter = '$ID=' . $_SESSION['userID'] . '||$fileURL=' . $fileURL. '||$description=' . $_POST['description'][$f];

		  $cmd = $fm->newPerformScriptCommand($tFile,'CreateFileRecord', $parameter);
		  $result = $cmd->execute(); 

		  if (FileMaker::isError($result)) { 
		  // display script error
			  echo '<p>FileMaker Script Error: ' . $result->getMessage() . '</p>'; 
			  exit;
		  } 
	   } 
	  
	   // ***********************************************
		  
	} else {
			$message[] = "Sorry, there was an error uploading your file (" . $rename . ").<br>";
	}
 }
}
// **************************************************************
}


?> 

      <div class="mt-1">
        <h1>Upload a File</h1>
      </div>
      <p class="lead">Please use this form to upload one or more files.<br>
      <span class="text-danger">NOTE: adding the ability for users to upload files to your web server is a major security risk!  Please do your research before adding this ability.</span>
      <br>PHP to upload multiple files to your server.<br>
        For MySQL: PHP to create each File record.<br>
        For FileMaker: FileMaker script to create each File record and insert uploaded file into container field <em>(will only work from pages located on FileMaker Server)</em>.</p>
      <?php 
      		include(FEEDBACK);
		?>
 
      
<div class="table-responsive">
<form class="form" id="uploadForm" action="<?php echo PAGE  . 'post_login/upload_many'; ?>" method="POST" enctype="multipart/form-data">


 <table id="myTable" class="order-list table table-striped table-hover table-sm ">
    <thead>
        <tr>
        	<td scope="col">&nbsp;</td>
            <td scope="col">Description</td>
            <td scope="col">File</td>
        	<td scope="col">&nbsp;</td>
        </tr>
    </thead>

    <tbody>
        <tr>
        	<td scope="row">1</td>
            <td>
                <input type="text" name="description[]" required="required" class="form-control">
            </td>
            <td>
                <input type="file" name="fileToUpload[]" required="required" class="form-control" onChange="ValidateFile(this);">
            </td>
            <td><a class="deleteRow"></a></td>
        </tr>
    </tbody>

    <tfoot>
        <tr>
        	<td scope="row"></td>
            <td colspan="5">
                <input type="button" class="btn btn-lg btn-block " id="addrow" value="Add Row">
        	</td>
        </tr>
    </tfoot>
</table>

<button class="btn btn-lg btn-primary btn-block" type="submit" name="UploadForm" value="Upload">Upload</button>
</form>
</div>

                <div id="loadingMessage" style="display:none" class="container alert alert-info" role="alert text-center">
				Please wait while uploading...
				<br><img src="assets/img/uploading.gif" width="32" height="32" alt="">
        		</div>
      
<?php

// DISPLAY UPLOADED FILES *****************************************************

      		if($continue == 1 && !empty($count)){
			// files uploaded
			echo "<p class=\"text-info\">File(s) Uploaded Successfully!</p>";
			  foreach ($links as $link) {
				echo "<image src=\"" . $link . "\" alt=\"Uploaded File Not Found\">";
				echo "<br>";
			  }
			}
			
			
if (isset($_POST['UploadForm']) && $continue == 1 ) {  		
// DEBUG *****************************************************
      		echo "<br>";
      		echo "<h3>FilePaths</h3>";
      		echo "<br>";
      		echo "<b>continue =  </b>" . $continue;
      		echo "<br>";
      		echo "<b>fileURL =  </b>" . $fileURL;
      		echo "<br>";
      		echo "<b>dirname =  </b>" . dirname($target_file) ;
      		echo "<br>";
      		echo "<b>realpath = </b>" . realpath($target_file) ;
      		echo "<br>";
      		echo "<br>";
      		echo "<b>renamed filename = </b>" . $rename ;
      		echo "<br>";
      		echo "<b>SERVER[DOCUMENT_ROOT] = </b>" . $_SERVER['DOCUMENT_ROOT'];
      		echo "<br>";
      		echo "<b>SERVER[SCRIPT_FILENAME] = </b>" . $_SERVER['SCRIPT_FILENAME'];
      		echo "<br>";
      		echo "<b>SERVER[PHP_SELF]  = </b>" . $_SERVER['PHP_SELF'];
      		echo "<br>";
      		echo "<b>directory for SERVER[PHP_SELF]  = </b>" . dirname($_SERVER['PHP_SELF']);
      		echo "<br>";
      		echo "<b>ROOT  = </b>" . ROOT;
      		echo "<br>";
      		echo  print_r($links);
      		echo "<p>&nbsp;</p>";
      		
// DEBUG *****************************************************
}
?>