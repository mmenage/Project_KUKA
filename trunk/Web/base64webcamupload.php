<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Récupération de l'objet envoyé
	$data = file_get_contents('php://input');
	
	$UploadDirectory	= 'D:/wamp/www/Kukka/uploads/'; // Dossier de destination

	$Random_Number = rand(0, 9999999999); //Numéro aléatoire pour le nom du fichier.
	
	$img = $data;
	$img = str_replace('base64image:data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	file_put_contents($UploadDirectory.'webcam'.$Random_Number.'.png', $data);
	
	// Création d'un tableau pour un envoie en jSon
	$return = array('error'=>'null',
					'method'=>'POST',
					'src'=>$UploadDirectory.'webcam'.$Random_Number.'.png',
					'success'=>true);
}
else{
$return = array('error'=>'403',
				'method'=>$_SERVER['REQUEST_METHOD']);
}
// envoie en jSon
echo json_encode($return);
?>