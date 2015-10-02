<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
	$data = file_get_contents('php://input');
	
	$ListOfPoints = explode("|", $data);
	
	$json = '{ "dessin":[{';
	for ($i=0; $i<count($ListOfPoints)-1; $i++){
		$currentPoint = explode(";", $ListOfPoints[$i]);
		$firstPoint = substr($currentPoint[0], 1, -1);
		$secondPoint = substr($currentPoint[1], 1, -1);
		
		$firstPoint =  explode(",", $firstPoint);
		$secondPoint =  explode(",", $secondPoint);
		
		if ($i != count($ListOfPoints)-2){
		$json .= '"L'.($i+1).'":[{"point1":"'.$firstPoint[0].';'.$firstPoint[1].'"},{"point2":"'.$secondPoint[0].';'.$secondPoint[1].'"}],';
		}
		else{
		$json .= '"L'.($i+1).'":[{"point1":"'.$firstPoint[0].';'.$firstPoint[1].'"},{"point2":"'.$secondPoint[0].';'.$secondPoint[1].'"}]';
		}
	}
	$json.="}]}";
	$return = array('error'=>$json,
					'method'=>'POST',
					'success'=>true);
}
else{
$return = array('error'=>'403',
				'method'=>$_SERVER['REQUEST_METHOD']);
}
echo json_encode($return);
?>