<!DOCTYPE html>
<html lang="en">
<head>
  <title>Dessin</title>
  <meta charset="utf-8">
  
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" href="css/style.css">
  <link href="style/style.css" rel="stylesheet" type="text/css">
  <script src="js/jquery.js"></script>
  <script src="js/jquery-migrate-1.1.1.js"></script>
  <script src="js/script.js"></script>
  <script src="js/superfish.js"></script>
  <script src="js/jquery.equalheights.js"></script>
  <script src="js/jquery.mobilemenu.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/tmStickUp.js"></script>
  <script src="js/jquery.ui.totop.js"></script>
  <script>
   $(document).ready(function(){
    $().UItoTop({ easingType: 'easeOutQuart' });
    $('#stuck_container').tmStickUp({});
    });
  </script>
  <!--[if lt IE 9]>
   <div style=' clear: both; text-align:center; position: relative;'>
     <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
       <img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
     </a>
  </div>
  <script src="js/html5shiv.js"></script>
  <link rel="stylesheet" media="screen" href="css/ie.css">
  <![endif]-->
  <!--[if lt IE 10]>
  <link rel="stylesheet" media="screen" href="css/ie1.css">
  <![endif]-->
</head>
<body class="page1" id="top">

<?php include("header.php"); ?>
<!--=====================
          Content
======================-->
	<div id="upload-wrapper" style="margin-bottom:50px;">
		  <h3>Dessine !</h3>
			
		  <!--<center><button id="snap">Preview</button> <br/></center>-->
		  <style type="text/css">
			#imageView, #imagePreview { border: 1px solid #000;background:white; }
			#imageTemp { position: absolute; top: 1px; left: 1px; }
		  </style>

		  <p>
			<label><span style="color:white;">Outils de dessins:</span>
			  <select id="dtool">
				<option value="line">Ligne</option>
				<option value="rect">Rectangle</option>
				<option value="pencil">Pinceau</option>
			  </select>
			</label>
		  </p>

		<div id="container" style="position: relative;">
			<div id="canvasDiv" style="float:left;">
				<canvas id="imageView"  width="600" height="400" ></canvas>
				<canvas id="imageTemp" width="600" height="400"></canvas>
			</div>
			<img id="imagePreview" width="600" height="400"  style="float:right;display:none;"/>
			<div style="clear:both"></div><br/>
			<div id="point" style="display:none;"></div>
			<div id="ListOfPoints" style="display:none;"></div>
		</div>
		<br/>
		<center><button id="sendToRobot">Envoyer au robot</button></center>
	</div>
	<script type="text/javascript" src="js/drawing.js"></script>
<script>
	document.addEventListener("mousemove", function() {
		
		var canvas = document.getElementById('imageView'),
		dataUrl = canvas.toDataURL(),
		imageFoo = document.createElement('img');
		imageFoo.src = dataUrl;

		// Style your image here
		imageFoo.style.width = '600px';
		imageFoo.style.height = '400px';

		// After you are done styling it, append it to the BODY element
		document.getElementById("imagePreview").src = imageFoo.src;
	});
	var canvas = document.getElementById("canvasDiv");
	canvas.addEventListener("mousedown", getPosition, false);
	canvas.addEventListener("mouseup", getPosition, false);
	
	function getPosition(event){
		$("#container").css("position","")
		var x1 = document.getElementById("canvasDiv").offsetLeft;
		var x2 = document.getElementById("canvasDiv").offsetWidth + x1;
		var y1 = document.getElementById("canvasDiv").offsetTop;
		var y2 = document.getElementById("canvasDiv").offsetHeight + y1;	
		var x = event.clientX;
		var y = event.clientY;

		var xMax = document.getElementById("canvasDiv").offsetWidth;
		var yMax = document.getElementById("canvasDiv").offsetHeight;

		var Negative = -1 ;

		var posX = x - x1 ;
		var posY = y - y1 - yMax;
		var posY = posY * Negative;
		var posY = posY-208;
		if(event.type == "mouseup"){
			if ($( "#dtool" ).val() == "line"){
				if ($("#point").text() != ""){
					$("#ListOfPoints").append("("+$("#point").text()+");"+"("+(posX) + ',' + (posY)+")|");
				}
			}
			else if ($( "#dtool" ).val() == "rect"){
				var point = $("#point").text().split(",");
				
				x1 = point[0]  ;  y1 = point[1] ;    // First diagonal point
				x2 = posX  ;  y2 = posY ;    // Second diagonal point

				x3 = x1;
				y3 = y2;
				
				x4 = x2;
				y4 = y1;
				
				// $("#ListOfPoints").append("("+x1+","+y1+");"+"("+x3+ ',' + y3+")|("+x1+","+y1+");"+"("+x4+ ',' + y4+")|("+x2+","+y2+");"+"("+x4+ ',' + y4+")|("+x2+","+y2+");"+"("+x3+ ',' + y3+")|");
				$("#ListOfPoints").append("("+x1+","+y1+");"+"("+x3+ ',' + y3+")|("+x3+","+y3+");"+"("+x2+ ',' + y2+")|("+x2+","+y2+");"+"("+x4+ ',' + y4+")|("+x4+","+y4+");"+"("+x1+ ',' + y1+")|");
			}
			$("#point").text("");
		}
		else if(event.type == "mousedown"){
			$("#point").text((posX) + ',' + (posY));
		}
		$("#container").css("position", "relative");
		
	}
	
	$("#sendToRobot").click(function() {
		var draw = "des:"+$("#ListOfPoints").text();
		$.ajax({
			url: 'server.php',
			type: 'POST',
			data: draw,
			dataType: 'text',
			success: function(response) {
				response = JSON.parse(response);
				if (response.error == "null"){
					
					if (response.success == true){
						alert("Envoyé avec succès !");
					}
					else {
						alert("Non envoyé !");
					}
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) { 
				alert("Status: " + textStatus); alert("Error: " + errorThrown); 
			} 
		});
	});
	</script>

<?php include("footer.php"); ?>

</body>
</html>