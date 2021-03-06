<?php

include('include/DBHandler.php');

$DBhandler = new DBHandler();

if (isset($_POST['signup'])){

	$signupEmail = trim($_POST['signupEmail']);
	$password = trim($_POST['newPassword']);

	$MD5_password = md5($password);
			
	$results = $DBhandler->addUser($signupEmail, $MD5_password);
	
	if(!$results)
		$message =  "<div class='alert alert-danger'>Someting went wrong!<br>Please try again.</div>"; 	
	else
		$message =  "<div class='alert alert-success'>You are now registered.<br>You can now sign in.</div>"; 	

}

?>

<!DOCTYPE html>
<!-- saved from url=(0040)http://getbootstrap.com/examples/signin/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="http://getbootstrap.com/assets/ico/favicon.png">
    <title>webmail2task - login</title>
    <!-- Bootstrap core CSS -->
    <link href="http://getbootstrap.com/dist/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="http://getbootstrap.com/examples/signin/signin.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
  <style type="text/css"></style></head>
  <body>
    <div class="container">

		<form class="form-signin" method='post' id='block-validate' action='signup.php'>

			<?=$message ?>
      
	        <h2 class="form-signin-heading">Please enter your email</h2>
	        
			<div class="control-group" style="margin-bottom: 10px;">
				<div class="controls">
			        <input type="email" class="form-control" placeholder="Valid e-mail address" id="email" name="email">
				</div>
			</div>
			
			<button class="btn btn-lg btn-primary btn-block" type="submit" name="signup">Recover password</button>      
			
	        <ul class='list-inline' style="position: relative; top: 10px;"> 
	            <li><a href='login.php' id='login'>Sign in</a></li> 
	            <li><a href='forgot_password.php' id='forgotPasssword'>Forgot Password</a></li> 
	            <li><a href='signup.php' id='signup'>Signup</a></li> 
	        </ul> 

		</form>

    </div> <!-- /container -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="assets/js/lib/jquery.tablesorter.min.js"></script>
    <script type='text/javascript' src='assets/js/lib/jquery.validationEngine.js'></script>
    <script type='text/javascript' src='assets/js/lib/languages/jquery.validationEngine-en.js'></script>
    <script type='text/javascript' src='assets/js/lib/jquery.validate.min.js'></script>
    <script type='text/javascript' src='assets/js/main.js'></script>
	<script>		
        $(function() {
            formValidation();
        });
	</script>
</body></html>
