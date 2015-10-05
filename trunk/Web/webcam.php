<!DOCTYPE html>
<html lang="en">
<head>
  <title>WebCam</title>
  <meta charset="utf-8">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" href="css/style.css">
  <link href="style/style.css" rel="stylesheet" type="text/css">
  <link href="css/ihover.css" rel="stylesheet" type="text/css">
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
		<div align="center">
			<h3>Envoyer vos fichiers !</h3>
			
			<button id="snap">Prendre une photo</button> <br/>
			<video id="video" width="" height="480" autoplay style="height:300px; margin-top:20px;float:left;"></video>

			<canvas id="canvas" width="" height="480" style="display:none;"></canvas>
			
			<div style="height:480px; margin-top:20px;float:right;"><img id="imagepreview" style="height:300px;"/></div>
			<div style="clear:both"></div>
			 <br/>
			<button id="sendToRobot">Envoyer au robot</button>
		</div>
	</div>

	<script>
	$("#sendToRobot").click(function() {
		var cam = "cam:"+$("#imagepreview").attr("src");
		$.ajax({
			url: 'server.php',
			type: 'POST',
			data: cam,
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

	window.addEventListener("DOMContentLoaded", function() {
		// Grab elements, create settings, etc.
		var canvas = document.getElementById("canvas"),
			context = canvas.getContext("2d"),
			video = document.getElementById("video"),
			videoObj = { "video": true },
			errBack = function(error) {
				console.log("Video capture error: ", error.code); 
			};

		// Put video listeners into place
		if(navigator.getUserMedia) { // Standard
			navigator.getUserMedia(videoObj, function(stream) {
				video.src = stream;
				video.play();
			}, errBack);
		} else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
			navigator.webkitGetUserMedia(videoObj, function(stream){
				video.src = window.webkitURL.createObjectURL(stream);
				video.play();
			}, errBack);
		}
		else if(navigator.mozGetUserMedia) { // Firefox-prefixed
			navigator.mozGetUserMedia(videoObj, function(stream){
				video.src = window.URL.createObjectURL(stream);
				video.play();
			}, errBack);
		}
	}, false);

	document.getElementById("snap").addEventListener("click", function() {
		canvas.width = 640;
		canvas.height = 480;
		canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
		var data = canvas.toDataURL('image/png');
		
		$("#imagepreview").attr("src", data);
		var base64image = "base64image:"+$("#imagepreview").attr("src");
		$.ajax({
			url: 'base64webcamupload.php',
			type: 'POST',
			data: base64image,
			dataType: 'text',
			success: function(response) {
				response = JSON.parse(response);
				$("#imagepreview").attr("src", response.src);
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