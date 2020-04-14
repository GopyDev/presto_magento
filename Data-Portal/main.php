<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Eckinger">
    <meta name="keyword" content="Dashboard, Bootstrap, Classroom">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>PrestoFresh Data Manager</title>

    <!-- Bootstrap core CSS -->
    <!-- <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- Custom styles for this template -->
    <link href="style1.css" rel="stylesheet">
    <link href="./css/datepicker.css" rel="stylesheet">

	<!-- <script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>    
    <script type='text/javascript' src='js/bootstrap.js'></script> -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="./js/bootstrap-datepicker.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <script>
      jQuery(function ($) {
        $('[rel=tooltip]').tooltip();
        $("#logout").click(function(){
          location.href="php/logout.php";
        });
        $('.datepicker').datepicker();
        $('#fromdate').hide();
        $('#todate').hide();
      });
      function updateChar() {
        var zone = document.getElementById("select");
        var radios = document.getElementById("range_custom");
        if (zone.value == "1"){
            $('#report').val(1);  
            if (radios.checked)
            {
              $('#fromdate').show();
              $('#todate').show();
            }
            else {
              $('#fromdate').hide();
              $('#todate').hide(); 
            }
              
        } else  if (zone.value == "2"){
            $('#report').val(2);     
            if (radios.checked)
            {
              $('#fromdate').show();
              $('#todate').show();
            }
            else {
              $('#fromdate').hide();
              $('#todate').hide(); 
            }
        } else {
          $('#report').val(1);
        }        
      }
      function setDatePickerVisible(obj) {
        if (obj[0].checked && obj[0].value == 4) {
          $('#fromdate').show();
          $('#todate').show();
        } else {
          $('#fromdate').hide();
          $('#todate').hide();
        }
      }
    </script>
    <style>
      .form-group {
        color: green;
      }
      .login-btn {
        width: 120px;
        float: right;
      }
      .container {
        margin-top: 30px;
      }

    </style>
</head>
  <?php
    session_start();
    if (!$_SESSION['username']) {
      header('Location: index.php');
    }
  ?>
  <body class="login-body">

    <div class="container">
      <div class="content">
        <div class="row">
          <div class="col-sm-6">
          </div>
          <div class="col-sm-6">
            <button class="btn btn-login btn-block login-btn" id="logout">Log out</button>
          </div>
        </div>
        <form action="php/exportData.php">
          <div class="form-group">
            <select class="" id="select" onchange="updateChar()";>
              <option selected>Select the report you wish to run</option>
              <option value="1">Recent Customers Report</option>
              <option value="2">Registered Customers - Never Purchased</option>
            </select>
            <input type="hidden" name="report" id="report" value="1"/>
          </div>
          <div class="form-group" id="daterange">
            <div>
              <input type="radio" name="options" class="option" id="range_365" value="1" autocomplete="off" onclick="setDatePickerVisible($(this))" checked> Last 365 Days
            </div>
            <div>
              <input type="radio" name="options" class="option" id="range_90" value="2" autocomplete="off" onclick="setDatePickerVisible($(this))"> Last 90 Days
            </div>
            <div>
              <input type="radio" name="options" class="option" id="range_30" value="3" autocomplete="off" onclick="setDatePickerVisible($(this))"> Last 30 Days
            </div>
            <div>
              <input type="radio" name="options" class="option" id="range_custom" value="4" autocomplete="off" onclick="setDatePickerVisible($(this))"> Custom Date Range 
            </div>
          </div>
          <div class="form-group" id="fromdate">
            <label>Start Date</label>
            <input class="datepicker" name="fromdate">
          </div>
          <div class="form-group" id="todate"> 
            <label>End Date &nbsp</label>
            <input class="datepicker" id="todate" name="todate">
          </div>
          <div class="form-group">
            <button class="btn btn-success" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
