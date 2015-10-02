<!DOCTYPE html>
<html lang="en">
<head>
  <title>SVG</title>
  <meta charset="utf-8">
  <meta name="format-detection" content="telephone=no">
  <link rel="icon" href="images/favicon.ico">
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="stylesheet" href="css/stuck.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/ihover.css">
  <script src="js/jquery.js"></script>
  <script src="js/jquery-migrate-1.1.1.js"></script>
  <script src="js/superfish.js"></script>
  <script src="js/script.js"></script>
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
	<script type="text/javascript" src="js/jquery.form.min.js"></script>
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
<?php
function getLoadedFiles(){
	$dir    = 'uploads';
	$imagesUploaded = scandir($dir);
	$h=1;
	for ($i = 2; $i<count($imagesUploaded); $i++){
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
}

?>
<script type="text/javascript">
$(document).ready(function() { 
	var options = { 
			target:   '#output',   // target element(s) to be updated with server response 
			beforeSubmit:  beforeSubmit,  // pre-submit callback 
			success:       afterSuccess,  // post-submit callback 
			uploadProgress: OnProgress, //upload progress callback 
			resetForm: true        // reset the form after successful submit 
		}; 
		
	 $('#MyUploadForm').submit(function() { 
			$(this).ajaxSubmit(options);  			
			// always return false to prevent standard browser submit and page navigation 
			return false; 
		}); 
		

//function after succesful file upload (when server response)
function afterSuccess()
{
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
	$('#progressbox').delay( 1000 ).fadeOut(); //hide progress bar

}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
	{
		
		if( !$('#FileInput').val()) //check empty input filed
		{
			$("#output").html("Are you kidding me?");
			return false
		}
		
		var fsize = $('#FileInput')[0].files[0].size; //get file size
		var ftype = $('#FileInput')[0].files[0].type; // get file type
		

		//allow file types 
		switch(ftype)
        {
            case 'image/png': 
			case 'image/gif': 
			case 'image/jpeg': 
			case 'image/pjpeg':
			case 'image/svg+xml':
			case 'application/pdf':
                break;
            default:
                $("#output").html("<b>"+ftype+"</b> Unsupported file type!");
				return false
        }
		
		//Allowed file size is less than 5 MB (1048576)
		if(fsize>5242880) 
		{
			$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big file! <br />File is too big, it should be less than 5 MB.");
			return false
		}
				
		$('#submit-btn').hide(); //hide submit button
		$('#loading-img').show(); //hide submit button
		$("#output").html("");  
	}
	else
	{
		//Output error to older unsupported browsers that doesn't support HTML5 File API
		$("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
		return false;
	}
}

//progress bar function
function OnProgress(event, position, total, percentComplete)
{
    //Progress bar
	$('#progressbox').show();
    $('#progressbar').width(percentComplete + '%') //update progressbar percent complete
    $('#statustxt').html(percentComplete + '%'); //update status text
    if(percentComplete>50)
        {
            $('#statustxt').css('color','#000'); //change status text to white after 50%
        }
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

}); 

var loadFile = function(event) {
	var output = document.getElementById('imagepreview');
	output.src = URL.createObjectURL(event.target.files[0]);
};
</script>
<link href="style/style.css" rel="stylesheet" type="text/css">
	<div id="upload-wrapper" style="margin-bottom:50px;">
		<div align="center">
			<h3>Envoyer vos fichiers !</h3>
			<form action="processupload.php" method="post" enctype="multipart/form-data" id="MyUploadForm" style="margin-bottom:20px;">
				<input name="FileInput" id="FileInput" type="file" onchange="loadFile(event)"/>
				<input type="submit"  id="submit-btn" value="Upload" />
				<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
			</form>
			<div id="progressbox" ><div id="progressbar"></div ><div id="statustxt">0%</div></div>
			<div id="output"></div>
			<table id="uploadedFiles" style="width:360px;float:left;">
				<?php
					getLoadedFiles();
				?>
			</table>
			
			<div style="height:300px; margin-top:20px;float:right;"><img id="imagepreview" style="max-width:360px; max-height:300px;"/></div>
			<div style="clear:both"></div>
		</div>
	</div>

	<script>
	$(".imageStored").click(function() {
		$("#imagepreview").attr("src", $(this).attr( 'src' ));
	});
	$("#submit-btn").click(function() {
		$("#uploadedFiles").remove();
	});
	</script>

<?php include("footer.php"); ?>

</body>
</html>