<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Eckinger">
    <meta name="keyword" content="Dashboard, Bootstrap, Classroom">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>Classroom - Login</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet">
  <script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>    
  <script type='text/javascript' src='js/bootstrap.js'></script>
  <script type="text/javascript" src="js/mine.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <script>
      jQuery(function ($) {
        $('[rel=tooltip]').tooltip();
      });    
    </script> 
</head>
  <body class="login-body">
    <div class="container">

      <form class="form-login" method="post" action=".\php\loginConfirm.php" >
      	<div class="login-header">
			    PrestoFresh Data Manager
        </div>
        <div class="login-wrap">
          <div class="input-group">
            <span class="input-group-addon" id="basic-addon1"><img src="img/user-login-icon.png" alt="Info"/></span>
            <input type="text" id="text-login1" name="text-login1" class="form-control" placeholder="Username" autofocus>
          </div>	
          <div class="clr20"></div>
          <div class="input-group">
            <span class="input-group-addon" id="basic-addon2"><img src="img/password-login-icon.png" alt="Info"/></span>
            <input type="password" id="text-login2" name="text-login2" class="form-control" placeholder="Password">
          </div>	
          <div class="clr20"></div>
          <button class="btn btn-login btn-block" type="submit">LOGIN</button>
          <div class="clr20"></div>
        </div>
      </form>
      <?php
        session_start();
        if ($_SESSION['incorrect-login']) {
      ?>
        <script>
          alert("Incorrect Login");
        </script>
      <?php
        }
      ?>
    </div>
   
  </body>
  
</html>
