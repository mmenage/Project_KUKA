<?php
if(isset($_FILES["FileInput"]) && $_FILES["FileInput"]["error"]== UPLOAD_ERR_OK)
{
	$UploadDirectory	= 'D:/wamp/www/Kukka/uploads/'; //Dossier de destination
	
	// Check si la requête est une requête ajax
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
		die();
	}
	
	//Limite de taille
	if ($_FILES["FileInput"]["size"] > 5242880) {
		die("File size is too big!");
	}
	
	//Type de fichier autorisés
	switch(strtolower($_FILES['FileInput']['type']))
		{
            case 'image/png': 
			case 'image/gif': 
			case 'image/jpeg': 
			case 'image/pjpeg':
			case 'image/svg+xml':
				break;
			default:
				die('Unsupported File!');
	}
	
	$File_Name          = strtolower($_FILES['FileInput']['name']);
	$File_Ext           = substr($File_Name, strrpos($File_Name, '.')); //Extension du fichier
	$Random_Number      = rand(0, 9999999999); //Numéro aléatoire pour le nom du fichier
	$NewFileName 		= $Random_Number.$File_Ext; //Nouveau nom du fichier
	
	if(move_uploaded_file($_FILES['FileInput']['tmp_name'], $UploadDirectory.$NewFileName )) // Si le fichier télécharger peut être déplacé
	   {
		// Création du html pour mettre à jour la liste
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