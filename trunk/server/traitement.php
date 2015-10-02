<?php
	$path_image= "assets/img/test.png"; //image path
	$img = imagecreatefrompng($path_image);
	$width = imagesx($img); //get width size from image
	$height = imagesy($img); //get height size from image
	$i=0;
	echo "Width : $width / Height : $height <br/>";

	for($x=1;$x<=$width;$x++){
	    for($y=1;$y<=$height;$y++){
	        $pixel=imagecolorat($img, $x, $y);
	        echo $pixel."#";
	        if($pixel == '0'){	
	        	$i++;
	        	echo '!'.$x.'/'.$y."! <br/>";
	        }
	    }
	}
	echo '<br/> Black pixel number : '.$i;
?>
