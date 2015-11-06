<?php
	require_once("assets/header.php");
	$path_image = "assets/img/square.png"; //image path
	$img = imagecreatefrompng($path_image);
	$width = imagesx($img); //get width size from image
	$height = imagesy($img); //get height size from image
	$colors = array(); //array creation to put all colors in this array
	echo "x: ".$width." / y: ".$height."<br/>"; //we show image size **DEBUG
	echo "Traitement de l'image :";
	echo "<br/><img src='".$path_image."'/><br/>"; //we display image source

	//TEST CHECK COLOR  
	echo "debut<br/>";
		//$pixel = $img->getImagePixelColor($width, $height);
		// $colors_test = $pixel->getColor();
		print_r($colors_test); // produces Array([r]=>255,[g]=>255,[b]=>255,[a]=>1);
		//$pixel->getColorAsString(); // produces rgb(255,255,255);
	echo "fin<br/>";
	//END TEST CHECK COLOR

	$file_w = fopen("output.txt","w");


	if($image_a_traiter && imagefilter($image_a_traiter, IMG_FILTER_GRAYSCALE)&&imagefilter($image_a_traiter, IMG_FILTER_CONTRAST, -255)){ 
		//If source image is present and 
		imagejpeg($image_a_traiter, 'assets/img/square.png');
	}

	else{
		echo "There is a problem.<br/>";
	}

	imagedestroy($image_a_traiter);
	echo "Noir et Blanc en PHP : <br/>";
	echo "<img src='assets/img/square.png'/><br/>";

	for($y = 0; $y < $height; $y++){
		$height_array = array();
		for($x = 0; $x < $width; $x++){
			$rgb = imagecolorat($image_traite, $x, $y);
	        $r = ($rgb >> 16) & 0xFF;
	        $g = ($rgb >> 8) & 0xFF;
	        $b = $rgb & 0xFF;

			$width_array = array($r, $g, $b);
			$height_array[] = $width_array;
		}
		$colors[] = $height_array;
		//print_r($colors);
		fwrite($file_w, $colors);
	}
?>