<?php
	session_start();

if (!isset($_SESSION['loginEmail'])){
	 include("login.php");
}
else {

include('include/DBHandler.php');

$DBhandler = new DBHandler();	

$email = $_SESSION['loginEmail'];	
$id = $DBhandler->getUserId($email);

// Get username from email

$parts = explode("@", $email);
$username = $parts[0];

if(isset($_POST['update'])){	

	$userPassword = $_POST['userPassword'];	
	
	$MD5_password = md5($userPassword);
					
	$result = $DBhandler->updateProfile($MD5_password,$id);	
	
	if($result)
		$message = "<div class='alert alert-success'>Password was successfully updated.</div>";
	else
		$message = "<div class='alert alert-error'>Error!</div>";		
	
}

?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Create task from emails">
		<meta name="author" content="obkonsult.com">
		<title>webmail2stask.com - profile</title>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">

		<link rel="shortcut icon" href="http://webmail2task.com/admin2/assets/ico/favicon.ico"> 
	</head>
	<body>
	
	
   <div class="container">

	      <!-- Static navbar -->
	      <div class="navbar navbar-default">

<!--
	        <div class="navbar-header">
	          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a class="navbar-brand" href="webmail2tasks.php">Home</a>
	        </div>
-->

	        <div class="navbar-collapse collapse">
	
			<ul class="nav navbar-nav">
	          
<!--
	            <li class="dropdown">
	                <a href="#" data-toggle="dropdown" class="dropdown-toggle">Settings <b class="caret"></b>
	
	                </a>
	                <ul class="dropdown-menu" id="menu1">
	                    <li>
	                        <a href="mailbox_settings.php">Mailbox access</a>
	                    </li>
	                </ul>
	            </li>
	
-->
	            <li class="dropdown">
	                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-user"></i> <?=$username ?> <i class="caret"></i>
	
	                </a>
	                <ul class="dropdown-menu">
	                    <li>
	                        <a tabindex="-1" href="profile.php">Profil</a>
	                    </li>
	                </ul>
	            </li>
	
			</ul>
	
	          <ul class="nav navbar-nav navbar-right">
	            <li><a href="logout.php">Logga out</a></li>
	          </ul>
	        </div><!--/.nav-collapse -->
	      </div>

                                
		  <form class="form-horizontal" method="post" action="profile.php" id="block-validate">
	     
			 <div class="row">
				 <div class="col-lg-4">            
					 <div> <?=$message ?></div>                                     
				</div>
			</div>                                        
		        
	        <legend>Updatera profilen</legend>

			<div class="row">
				<div class="col-lg-4">            
                    <div class="control-group">
                      <label class="control-label" for="mailbox">Epost</label>
                      <div class="controls">
                        <input class="form-control" type="text" name="email" id="email" value="<?=$email ?>">
                      </div>
                    </div>
				</div>
			</div>                                        

			<div class="row">
				<div class="col-lg-2">            
	                <div class="control-group">
	                  <label class="control-label" for="userPassword">L&ouml;senord</label>
	                  <div class="controls">                                            
						<input class="form-control" type="password" id="newPassword" name="newPassword" >                                          
	                  </div>
	                </div>
				</div>
			</div>                                        

			<div class="row">
				<div class="col-lg-2">            
	                <div class="control-group">
	                  <label class="control-label" for="userPassword">Bekr&auml;fta l&ouml;senord</label>
	                  <div class="controls">                                            
						<input class="form-control" type="password" id="confirmNewPassword" name="confirmNewPassword" >                                          
	                  </div>
	                </div>
				</div>
			</div>                                        

                           
			<div class="row" style="padding-top: 10px;">
				<div class="col-lg-6">            
                  <button type="submit" class="btn btn-primary" name="update">Updatera profilen</button>
				  <button id="back" class="btn btn-default">Avbryt</button>
				</div> 
			</div>                                       
                                                                                
		</form>

	</div>


	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="assets/js/lib/jquery.mousewheel.js"></script>
	<script type="text/javascript" src="assets/js/lib/jquery.nicescroll.min.js"></script>	
	<script type="text/javascript" src="assets/js/lib/prettify.js"></script>		
	<script type="text/javascript" src="assets/js/lib/jquery.tablesorter.min.js"></script>
	
    <script type='text/javascript' src="assets/js/lib/jquery.validationEngine.js"></script>
    <script type='text/javascript' src="assets/js/lib/jquery.validate.min.js"></script>
	<script type='text/javascript' src="assets/js/lib/languages/messages_sv.js"></script>        	                            

    <script type='text/javascript' src="assets/js/main.js"></script>
    <script type="text/javascript" src="assets/js/spin.min.js"></script> 
    
	<script>		
        $(function() {
            formValidation();
        });
              
		$(document).ready(function(){
		    $('#back').click(function(){
		        parent.history.back();
		        return false;
		    });
		});              
              
                
	</script>

    </body>

</html>

<?php
	
} 
?>