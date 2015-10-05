<?php
if(isset($_FILES["FileInput"]) && $_FILES["FileInput"]["error"]== UPLOAD_ERR_OK)
{
	$UploadDirectory	= 'D:/wamp/www/Kukka/uploads/'; //specify upload directory ends with / (slash)
	
	// check if this is an ajax request
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
		die();
	}
	
	//Is file size is less than allowed size.
	if ($_FILES["FileInput"]["size"] > 5242880) {
		die("File size is too big!");
	}
	
	//allowed file type Server side check
	switch(strtolower($_FILES['FileInput']['type']))
		{
			//allowed file types
            case 'image/png': 
			case 'image/gif': 
			case 'image/jpeg': 
			case 'image/pjpeg':
			case 'image/svg+xml':
				break;
			default:
				die('Unsupported File!'); //output error
	}
	
	$File_Name          = strtolower($_FILES['FileInput']['name']);
	$File_Ext           = substr($File_Name, strrpos($File_Name, '.')); //get file extention
	$Random_Number      = rand(0, 9999999999); //Random number to be added to name.
	$NewFileName 		= $Random_Number.$File_Ext; //new file name
	
	if(move_uploaded_file($_FILES['FileInput']['tmp_name'], $UploadDirectory.$NewFileName ))
	   {
		echo '<table id="uploadedFiles" style="width:360px; float:left;">';
		$dir    = 'uploads';
		$imagesUploaded = scandir($dir);
		$h=1;
		for ($i = 0; $i<count($imagesUploaded); $i++){
			$currentimage = $imagesUploaded[$i];
			$checkExtension = explode(".", $currentimage);
			
			if ($checkExtension[1] == "png" or $checkExtension[1] == "gif" or $checkExtension[1] == "jpeg" or $checkExtension[1] == "pjpeg" or $checkExtension[1] == "svg" or $checkExtension[1] == "pdf"){
				$imagefolder = "uploads/".$currentimage;
				if ($h==1){
					echo "<tr>";
				}
				$h++;
				echo '<td> <img class="imageStored" src="'.$imagefolder.'" width=80px height=80px/> '.$checkExtension[1].' </td>';
				if ($h==5){
					echo "<tr/>";
					$h=1;
				}
			}
		}
		echo '<table/>	<script>
	$(".imageStored").click(function() {
		$("#imagepreview").attr("src", $(this).attr( "src" ));
	});
	$("#submit-btn").click(function() {
		$("#uploadedFiles").remove();
	});
	</script>';

		die('<script> alert("Fichier envoyé !"); </script>');
	}else{
		die('<script> alert("Fichier non envoyé :("); </script>');
	}
	
}
else
{
	die('Something wrong with upload! Is "upload_max_filesize" set correctly?');
}