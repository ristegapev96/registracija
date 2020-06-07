<?php
// code to protect post-login pages

    // check to see whether the user is logged in or not
    if(empty($_SESSION['is_logged_in']))
    {
        // if they are not, redirect them to the login page
		header('Location: ' . PAGE . 'login');
		exit;
    }


$continue = null;
$message = array();
$uploadOK = 0;
$deleteAfterUpload = 0 ; // to delete file from dirctory after uploading, change to 1

$target_dir =  __DIR__. "/uploads/" .  $_SESSION['userID'] . "/" ;


if (isset($_POST["UploadForm"]) && $_FILES["fileToUpload"]['tmp_name'] == '') {
	$message[] = "Select a file to upload before clicking upload!";
} else {
	$continue = 1;
}

// CREATE THE UPLOAD FOLDER, CHECK THE FILE
if ( isset($_POST['UploadForm']) && $continue && !empty($_FILES["fileToUpload"]) && ($_FILES['fileToUpload']['error'] == 0) ) {
// did submit form, with a file and without an error

// rename file to remove unsafe characters
$rename = preg_replace( '`[^a-z0-9-_.]`i','_',basename($_FILES["fileToUpload"]["name"]) ); 

$target_file = $target_dir . $rename ;  // from the form input, file to be uploaded

$fileURL =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/uploads/" .  $_SESSION['userID'] . "/" .  $rename;

$fileURL = str_replace("index.php?page=","",$fileURL);

$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
if($check !== false) {
	$message[] = "File is an image - " . $check["mime"] . ".<br>";
} else {
	$message[] = "File is not an image.<br>";
}

// Check if file already exists
if (file_exists($target_file)) {
    $message[] = "File already existed and was overwritten.<br>";
}


// Check file size
if ($_FILES["fileToUpload"]["size"] > 2097152) {
    $message[] = "Sorry, your file is too large.<br>";
    $continue = 0;
} 


// Only allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif") {
    $message[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
    $continue = 0;
} 
}

// UPLOAD THE FILE
// Check if $continue is set to 0 by an error
if ( isset($_POST['UploadForm']) && $continue == 0 ){
    $message[] = "Sorry, your file was not uploaded.<br>";
}

// if everything is ok, try to upload file
if ( isset($_POST['UploadForm']) && $continue == 1 ){

// create upload directory if it doesn't already exist
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// upload a file
// if the destination file already exists, it will be overwritten
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $message[] = "The file ". $rename. " has been uploaded.<br>";  
        $uploadOK = 1;      	
    } else {
        $message[] = "Sorry, there was an error uploading your file.<br>";
    }
    	
	if( $uploadOK && $deleteAfterUpload && file_exists($target_file) ){
	   // delete the uploaded file
		   if (!unlink($target_file)){ 
			   $message[] = "Error deleting $target_file from directory after uploading.";
		   } else{
			   $message[] = "Deleted $target_file from directory after uploading.";
		   }
	}
}

// CREATE THE MYSQL DATABASE RECORD
if( isset($_POST['UploadForm']) && $uploadOK && $datasource == 'MySQL'){
 // MySQL *****************************************************
 // Find and update the User's MySQL record with the file's URL
		   /* promeneto vo komentar
		   $sql = 'UPDATE '. $tUser . ' SET fileURL = :fileURL WHERE id = :userID';
				$query = $conn->prepare($sql);
				$query->bindValue(':fileURL', $fileURL);
				$query->bindValue(':userID', $_SESSION['userID']);
				$query->execute();
		
				$errorInfo = $query->errorInfo();
	
				  if($errorInfo[0] != 0){
					   echo '<p>MySQL Update Error: ' . $errorInfo[2] . '</p>'; 
					   exit;
				  }	
			*/

 } 

// CREATE THE FILEMAKER DATABASE RECORD
if ( isset($_POST['UploadForm']) && $uploadOK && $datasource == 'FileMaker') {
 // FILEMAKER *****************************************************
 // Find and update the User's FileMaker record with the file's URL, then insert from URL to get that file into the container field

	   $cmd = $fm->newFindCommand($tUser);
	   $cmd->addFindCriterion('ID', $_SESSION['userID']);
	   $query = $cmd->execute();
	  
	   $recID = $query->getFirstRecord()->getRecordID();
	  
		if (!FileMaker::isError($query)) { 
			// once record found, update
			$cmd = $fm->getRecordById($tUser, $recID); 
			$cmd->setField('fileURL', $fileURL); 
			$query = $cmd->commit();
		
				if (FileMaker::isError($query)) { 
				// display update error
					echo '<p>FileMaker Update Error: ' . $query->getMessage() . '</p>'; 
					exit;
				} 

// Use a FileMaker script that uses the "Insert from URL" step to insert the image into the container from your fileURL field
			$parameter = '$ID=' . $_SESSION['userID'] ;

			$cmd = $fm->newPerformScriptCommand($tUser,'InsertUserFile', $parameter);
			$result = $cmd->execute(); 

			if (FileMaker::isError($result)) { 
			// display script error
				echo '<p>FileMaker Script Error: ' . $result->getMessage() . '</p>'; 
				exit;
			} 
			
			// get FileMaker Conatiner contents
			$cmd = $fm->newFindCommand($tUser);
			$cmd->addFindCriterion('ID', $_SESSION['userID']);
			$query = $cmd->execute();
			
			if (!FileMaker::isError($query)) { 
			   // put ALL found records into an array variable called $result
			   $image = $query->getFirstRecord()->getField('fileContainer');
			  }
			
	   } 
 }
?> 
	   <!--
      <div class="mt-1">
        <h1>Upload a File</h1>
      </div>
      <p class="lead">Please use this form to upload a file.<br>
      <span class="text-danger">NOTE: adding the ability for users to upload files to your web server is a major security risk!  Please do your research before adding this ability.</span>
      <br>PHP to upload a file to your server, and find and update the User record with the uploaded file's URL.<br>
        For FileMaker: FileMaker script to insert uploaded file into container field <em>(will only work from pages located on FileMaker Server)</em>.</p>-->
      <?php 
      		include(FEEDBACK);
      ?>
      
 <div>
 	<div id="rez"></div>
 	<?php 
 	
 	// dopolnitelno
 	$directory  = "post_login/uploads/".$_SESSION['userID'].'/';
 	//echo $directory;
 	$counter = 0;
	foreach (glob($directory . "*.{jpg,jpeg,png,gif}", GLOB_BRACE) as $filename) {
	    //echo "$filename size " . filesize($filename) . "<br>";
	    echo "<div style=' border: 1px solid black; position: relative; float: left; margin-right: 5px; margin-bottom: 5px;'><img src='".$filename."' style='height: 90px;'/><div id='../".$filename."' class='close' style='position:absolute; top: 0px; right: 4px; '><img src='img/delete.png' style='height: 20px;'/></div></div>";
	    $counter++;
	}
	//echo $counter;
	echo "<div style='clear: both;'></div>";
	?>

	<?php
	// dopolnitelno
 	if($counter<6){
 	?>
 	<form id="uploadForm" class="form" action="<?php echo PAGE  . 'post_login/upload'; ?>" method="POST" enctype="multipart/form-data">

        <fieldset>
        <div class="form-group">
        <label for="file" class="control-label">File</label>
        <input type="file" name="fileToUpload"></input>
        </div>
            
        
        <div class="form-group">
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="UploadForm" value="Upload">Upload</button>
        </div>
        </fieldset>
      </form></div>
      
      <?php }  ?>
	   <div id="loadingMessage" style="display:none" class="container alert alert-info" role="alert text-center">
		 Please wait while uploading...
		 <br><img src="assets/img/uploading.gif" width="32" height="32" alt="">
	   </div>

<?php 
// DISPLAY UPLOADED FILE *****************************************************
      		
      		if(!empty($fileURL) && $uploadOK == 1){
			// file uploaded
			/*echo "<p class=\"text-info\">File Uploaded Successfully!</p>";
			echo "<image src=\"" . $fileURL . "\" alt=\"Uploaded File Not Found\">";
			   if($datasource == 'FileMaker') {
			   echo "<br>";
			   echo "view FileMaker container:<br>";
			   // $host = FM Server IP from connections.php
			   echo '<image src="http://'.$host . $image .'">';
				echo "<br>";
			   }*/
			}
			
      		
if (isset($_POST["UploadForm"]) && $uploadOK == 1 ) {    		
// DEBUG *****************************************************
      		/*echo "<br>";
      		echo "<h3>FilePaths</h3>";
      		echo "<b>uploadOK =  </b>" . $uploadOK;
      		echo "<br>";
      		echo "<b>fileURL =  </b>" . $fileURL;
      		echo "<br>";
      		echo "<b>dirname =  </b>" . dirname($target_file) ;
      		echo "<br>";
      		echo "<b>realpath = </b>" . realpath($target_file) ;
      		echo "<br>";
      		echo "<b>orignial filename (basename) = </b>" . basename($_FILES["fileToUpload"]["name"]) ;
      		echo "<br>";
      		echo "<b>renamed filename = </b>" . $rename ;
      		echo "<br>";
      		echo "<b>SERVER[DOCUMENT_ROOT] = </b>" . $_SERVER["DOCUMENT_ROOT"];
      		echo "<br>";
      		echo "<b>SERVER[SCRIPT_FILENAME] = </b>" . $_SERVER['SCRIPT_FILENAME'];
      		echo "<br>";
      		echo "<b>SERVER[PHP_SELF]  = </b>" . $_SERVER['PHP_SELF'];
      		echo "<br>";
      		echo "<b>directory for SERVER[PHP_SELF]  = </b>" . dirname($_SERVER['PHP_SELF']);
      		echo "<br>";
      		echo "<b>ROOT  = </b>" . ROOT;
      		echo "<p>&nbsp;</p>";
      		*/
// DEBUG *****************************************************
}
?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script type="text/javascript">

		
		$(".close").click(function(){
			var checkstr =  confirm('Потврди го бришењето');
			if(checkstr == true){
			  var id = $(this).attr("id");
    			//alert(id);
    			
    			$.post('post_login/brisi.php', {pateka: id}, function(data){
			    	$('#rez').html(data);
			    	//alert("id");
			    	location.reload();
			    });
			    
    			

			}else{
			return false;
			}
			});

	</script>
