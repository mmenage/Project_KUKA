<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
	$data = file_get_contents('php://input');
	
	$UploadDirectory	= 'D:/wamp/www/Kukka/uploads/'; //specify upload directory ends with / (slash)

	$Random_Number = rand(0, 9999999999); //Random number to be added to name.
	
	$img = $data; // Your data 'data:image/png;base64,AAAFBfj42Pj4';
	$img = str_replace('base64image:data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	echo $img;
	$data = base64_decode($img);
	file_put_contents($UploadDirectory.'webcam'.$Random_Number.'.png', $data);
	

	$return = array('error'=>'null',
					'method'=>'POST',
					'success'=>true);
}
else{
$return = array('error'=>'403',
				'method'=>$_SERVER['REQUEST_METHOD']);
}
echo json_encode($return);
?>