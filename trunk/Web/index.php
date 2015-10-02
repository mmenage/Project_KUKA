<!DOCTYPE html>
<html lang="en">
<head>
  <title>Menu</title>
  <meta charset="utf-8">
  <meta name="format-detection" content="telephone=no">
  <link rel="icon" href="images/favicon.ico">
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="stylesheet" href="css/stuck.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/ihover.css">
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
<section class="content"><div class="ic"></div>
  <div class="container">
    <div class="row">
      <div class="grid_12">
        <div class="ta__center">
          <div class="banners">
            <a href="svg.php" class="banner">
              <img src="images/bann_svg.png" alt="">
              <div class="bann_capt"><span>SVG Image</span></div>
            </a>
            <a href="cdc.php" class="banner">
              <img src="images/bann_cdc.jpg" alt="">
              <div class="bann_capt"><span>Chaîne de caractère</span></div>
            </a>
            <a href="webcam.php" class="banner">
              <img src="images/bann_webcam.png" alt="">
              <div class="bann_capt"><span>Webcam</span></div>
            </a>
            <a href="dessin.php" class="banner">
              <img src="images/bann_dessin.jpg" alt="">
              <div class="bann_capt"><span>Dessin</span></div>
            </a>
          </div>
          <div class="clear"></div>
          <div class="row">
            <div class="grid_8 preffix_2">
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</section>

<?php include("footer.php"); ?>

</body>
</html>