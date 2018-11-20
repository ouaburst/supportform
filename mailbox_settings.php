<?php
session_start();

/*
 * Product: Supportform 
 * Version: 2.0
 * Author: Oualid Burstrom
 * Email: ob@obkonsult.com 
 * Data: 2014-01-31
 * 
 */


if (!isset($_SESSION['loginEmail'])){
	 include("login.php");
}
else {

include('include/DBHandler.php');

$DBhandler = new DBHandler();	

$email = $_SESSION['loginEmail'];	
$id = $DBhandler->getUserId($email);

$_SESSION['userID'] = $id;

$message = "";

//Get level
// level 1=Admin 2=Handläggare 3=Koordinator 4=Kund

$level = $DBhandler->getLevel($email);

if($level != 1)
	header("Location: logout.php");			

// Get username from email

$parts = explode("@", $email);
$username = $parts[0];

/* $showPasswordModal = false;		 */

// ---------------------- Add Mailbox ----------------------

if(isset($_POST['submit'])){	

	$data = array();
	
/*
	$serverType = $_POST['serverType'];	
	$pathPrefix = $_POST['pathPrefix'];	
	$port = $_POST['port'];
	$SSL = $_POST['SSL'];			
	$mailboxUser = $_POST['mailboxUser'];	
	$mailboxPassword = "";
	$hostname = $_POST['hostname'];
		
	if($SSL == 1)
		$SSL = "/ssl";
	else
		$SSL = "";
		
	if($serverType == 1)
		$serverType = "imap";
	else
		$serverType = "pop3";			
		
	$mailbox = "{".$hostname.":".$port."/".$serverType.$SSL."}".$pathPrefix;
	
	$data[] = $id;			
	$data[] = $serverType;
	$data[] = $pathPrefix;
	$data[] = $port;
	$data[] = $SSL;
	$data[] = $hostname;
	$data[] = $mailboxUser;
	$data[] = $mailboxPassword;
	$data[] = $mailbox;	
*/

	$serverType = $_POST['serverType'];	
	$pathPrefix = $_POST['pathPrefix'];	
	$mailboxUser = $_POST['mailboxUser'];	
	$mailboxPassword = "";
	$hostname = $_POST['hostname'];
		
	$SSL = "";
		
	if($serverType == 1){
		$serverTypeName = "pop3";
		$port = 110;
	}		

	if($serverType == 2){
		$serverTypeName = "imap/notls";
		$port = 143;
	}		

	if($serverType == 3){
		$serverTypeName = "imap";
		$port = 993;
		$SSLText = "/ssl";				
		$SSL = 1;						
	}		

	$mailbox = "{".$hostname.":".$port."/".$serverTypeName.$SSLText."}".$pathPrefix;
	
	$data[] = $id;			
	$data[] = $serverType;
	$data[] = $pathPrefix;
	$data[] = $port;
	$data[] = $SSL;
	$data[] = $hostname;
	$data[] = $mailboxUser;
	$data[] = $mailboxPassword;
	$data[] = $mailbox;	
				
	$result = $DBhandler->addMailBox($data);	
	
	if($result){
		$message = "<div class='alert alert-success'>Settings were successfully saved.<br>Please test your connection by pressing the <strong>Test connection</strong> button.</div>";
	}
	else
		$message = "<div class='alert alert-danger'>Error!</div>";		
	
}


// ---------------------- Update Mailbox ----------------------

if(isset($_POST['update'])){	

	$data = array();
		
	$serverType = $_POST['serverType'];	
	$pathPrefix = $_POST['pathPrefix'];	
	$mailboxUser = $_POST['mailboxUser'];	
	$mailboxPassword = $_POST['mailboxPassword'];
	$hostname = $_POST['hostname'];
	
	$group = $_POST['group'];	
	$category = $_POST['category'];	
	$status = $_POST['status'];	
	$prio = $_POST['prio'];		
		
	$SSL = "";
		
	if($serverType == 1){
		$serverTypeName = "pop3";
		$port = 110;
	}		

	if($serverType == 2){
		$serverTypeName = "imap/notls";
		$port = 143;
	}		

	if($serverType == 3){
		$serverTypeName = "imap";
		$port = 993;
		$SSLText = "/ssl";				
		$SSL = 1;						
	}		

	$mailbox = "{".$hostname.":".$port."/".$serverTypeName.$SSLText."}".$pathPrefix;
	
	$data[] = $id;			
	$data[] = $serverType;
	$data[] = $pathPrefix;
	$data[] = $port;
	$data[] = $SSL;
	$data[] = $hostname;
	$data[] = $mailboxUser;
	$data[] = $mailboxPassword;
	$data[] = $mailbox;	

	$data[] = $group;	
	$data[] = $category;	
	$data[] = $status;	
	$data[] = $prio;					
	
	
	$result = $DBhandler->updateMailboxSettings($data);	
	
	if($result){
		$message = "<div class='alert alert-success'>Informationen har uppdaterats.</div>";
		$DBhandler->setMailSettingsStatus(1,1);		// id=1, status=1 (OK)	
	}
	else
		$message = "<div class='alert alert-danger'>Error!</div>";		
	
}

// ---------------------- Get settings from database ----------------------

$data = array();

/* $testConnection = false; */
$updateSettings = false;

$settings = $DBhandler->getMailboxSettings();

if(count($settings)){

	$server_type = $settings[0];
	$path_prefix = $settings[1];
	$port = $settings[3];
	$SSLport = $settings[4];
	$hostname = $settings[5];
	$mailboxuser = $settings[6];
	$mailboxpassword  = $settings[7];			

	$checked1 = ($server_type == 1) ? "checked" : "";
	$checked2 = ($server_type == 2) ? "checked" : "";
	$checked3 = ($server_type == 3) ? "checked" : "";		

/*
	$statusResult = $DBhandler->checkMailSettingsStatus();	

	if($statusResult == 0){
		$message = "<div class='alert alert-success'>Settings were successfully saved.<br>Please test your connection by pressing the <strong>Test connection</strong> button.</div>";
		$testConnection = true;
	}
	
	$updateSettings = true;	
*/
}
else{
	
	$checked1 = "checked";
	$checked2 = "";	
	$checked3 = "";
	$path_prefix = "INBOX";
/* 	$port = "993"; */
}


// ---------------------- Save password ----------------------

/*
if(isset($_POST['savePassword'])){	 
	$mailboxPassword = $_POST['mailboxPassword'];	
	$_SESSION['mailboxPassword'] = $mailboxPassword;
	
	$testConnection = true;	
}
*/

// ---------------------- Check password session ----------------------

/*
if(!isset($_SESSION['mailboxPassword']) && count($settings))
	$showPasswordModal = true;	
*/

	


?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Create task from emails">
		<meta name="author" content="obkonsult.com">
		<title>Supporform V2</title>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">

		<link rel="shortcut icon" href="http://webmail2task.com/admin2/assets/ico/favicon.ico"> 
	</head>
	<body>
	
	
  <div class="container">

	   <!-- Static navbar -->
      <div class="navbar navbar-default">
        
        <div class="navbar-header">
          <span class="navbar-brand">Admin</span>
        </div>
        
        <div class="navbar-collapse collapse">

			<ul class="nav navbar-nav">
          
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle">Inst&auml;llningar <b class="caret"></b></a>
                
                <ul class="dropdown-menu" id="menu1">
                    <li>
                        <a href="admin.php">Anv&auml;ndare</a>
                    </li>
                    <li>
                        <a href="groups.php">Grupper</a>
                    </li>
                    <li>
                        <a href="category.php">Kategori</a>
                    </li>
                    <li>
                        <a href="status.php">Status</a>
                    </li>
                    <li>
                        <a href="priority.php">Prioritet</a>
                    </li>
                    <li>
                        <a data-toggle='modal' href='#emailSetupModal' onclick="emailSetupModal()">Epost</a>                        
                    </li>
                    <li>
                        <a href="mailbox_settings.php">IMAP/POP3</a>
                    </li>
                </ul>
                
            </li>
            
		</ul>

          <ul class="nav navbar-nav navbar-right">
            <li><a href="logout.php">Logga ut</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      
      </div><!--/. Static navbar -->	

                                
	     <form class="form-horizontal" method="post" action="mailbox_settings.php" id="block-validate">


			 <!-- ------------- Progress bar ----------------- -->
<!--
			 <div class="row">
				 <div class="col-lg-6">    

					<div class="progress">
					  <div id="bar" class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
					  </div>
					</div>

				</div>
			</div>
-->
			 <! / --------------- Progress bar ----------------- -->
	     
	        <legend>IMAP/POP3 inst&auml;llning</legend> 
	        
<!-- 	        <button type="button" class="btn btn-info btn-sm" style='margin-bottom: 10px;'>Help</button> -->
	        
<!-- 	        <a data-toggle='modal' href='#helpModal' class='btn btn-info' style='margin-bottom: 10px;'>Helpfull information</a> -->
	        
			<div id="spin" style="margin: 0 auto;"></div>                                        
			
			 <div class="row">
				 <div class="col-lg-5">    
					 <div id="message"> <?=$message ?></div>                                     
					 <div id="testResult"></div>	                                                                             
				</div>
			</div>                                        

			<?php if(!count($settings)){ ?>
			
			 <div class="row">			 				
				 <div class="col-lg-6">    
			  		<div class='alert alert-warning'>
			  			Please fill in IMAP/POP3 settings before using the service.<br>
			  			For more information please click on Helpfull Information button.			  			
			  		</div>
				</div>
			</div>                                        
								            
			<?php } ?>										
	        
			<div class="row">
				<div class="col-lg-4">            	                                                                                
	                <div class="control-group" style='margin-bottom: 10px;'>
	                  <label for="mailbox">E‑post servernamn</label>
	                  <div class="controls">
<!-- 	                    <input type="text" class="form-control" id="hostname" name="hostname" value="<?=$hostname ?>" placeholder="imap.gmail.com"> -->
	                    <input type="text" class="form-control" id="hostname" name="hostname" value="<?=$hostname ?>" >
	                  </div>
	                </div>                                        
				</div>
			</div>                                        

<!--
			<div class="row">
				<div class="col-lg-12">            	                                                                                
	                <div class="control-group" style='margin-bottom: 10px;'>
-->
						<div class='row'> 
							<div class='col-lg-12'> 
						        <div class='control-group' style='margin-bottom: 10px;'> 
						          <label for='mailboxUser'>Server typ</label> 
						          <div class='controls'> 
									<label class='checkbox-inline'> 
									  <input type='radio' name='serverType' id='serverType' value='1' <?=$checked1 ?>> POP3 – port 110 
									</label> 
									<label class='checkbox-inline'> 
									  <input type='radio' name='serverType' id='serverType' value='2' <?=$checked2 ?>> IMAP – port 143
									</label> 
									<label class='checkbox-inline'> 
									  <input type='radio' name='serverType' id='serverType' value='3' <?=$checked3 ?>> IMAP SSL (IMAPS) – port 993
									</label> 									
						          </div> 
						        </div> 
							</div> 
						</div>
<!--
	                </div>                                        
				</div>
			</div>                                        
-->

			<div class="row">
				<div class="col-lg-2">            	                                                                                
	                <div class="control-group" style='margin-bottom: 10px;'>
<!-- 	                  <label for="mailbox">S&ouml;kv&auml;gprefix</label> <a id="pathPrefix" href="#"> <i class="glyphicon glyphicon-question-sign"></i></a> -->
	                  <label for="mailbox">S&ouml;kv&auml;gprefix</label>
	                  <div class="controls">
						  <input type="text" class="form-control" id="pathPrefix" name="pathPrefix" value="<?=$path_prefix ?>">
	                  </div>
	                </div>                                        
				</div>
			</div>                                        

<!--
			<div class="row">
				<div class="col-lg-2">            	                                                                                
	                <div class="control-group" style='margin-bottom: 10px;'>
	                  <label for="mailbox">Port number</label>
	                  <a id="port" href="#"> <i class="glyphicon glyphicon-question-sign"></i></a>	                  
	                  <div class="controls">
						  <input type="text" class="form-control" id="port" name="port" value="<?=$port ?>">
	                  </div>
	                </div>                                        
				</div>
			</div>                                        


			<div class="row">
				<div class="col-lg-4">            	                                                                                
	                <div style='margin-bottom: 10px;'>				
	                  <label class="checkbox-inline">
	                  <div class="controls">
	                    <input type="checkbox" name="SSL" value="1" <?=$checked3 ?>> <strong>Use SSL</strong>
	                  </div>
	                </div>                                        
				</div>
			</div>                                        
-->
	
			<div class="row">
				<div class="col-lg-4">                                                    
	                <div class="control-group" style='margin-bottom: 10px;'>
	                  <label for="mailboxUser">Epostadress / anv&auml;ndarnamn</label>
	                  <div class="controls">
<!-- 	                    <input type="text" class="form-control" id="mailboxUser" name="mailboxUser" value="<?=$mailboxuser ?>" placeholder="Enter mailbox user"> -->
	                    <input type="text" class="form-control" id="mailboxUser" name="mailboxUser" value="<?=$mailboxuser ?>">
<!-- 	                    <span class="help-block">You will be prompted for the password.</span> -->
	                  </div>
	                </div>
				</div>
			</div>


			<div class="row">
				<div class="col-lg-2">            
	                <div class="control-group" style='margin-bottom: 10px;'>
	                  <label for="mailboxPassword">L&ouml;senord</label>
	                  <div class="controls">
	                        <input class="form-control" type="password" id="mailboxPassword" name="mailboxPassword" value="<?=$mailboxpassword ?>">
<!-- 	                        <span class="help-block">(The password is stored in a session)</span> -->
	                  </div>
	                </div>
				</div>
			</div>

			<div class="row" style="padding-top: 10px;">
				<div class="col-lg-6">            
						<a class="btn btn-warning" href="javascript:testConnection()">Testa f&ouml;rbindelsen</a>
				</div> 
			</div>                                       
							
							
																		                                        	
	        <br><legend>&Ouml;vriga inst&auml;llningar</legend> 
	        <p>N&auml;r ett epostmeddelande omvandlas till ett &auml;rende anv&auml;nds f&ouml;ljande default v&auml;rden:</p>
	        
	        <?php
				$mailboxSettings = array();
				$mailboxSettings = $DBhandler->getMailboxSettings();

				$groupList = array();		
				$groupList = $DBhandler->getGroupIDs();
				
				$prioList = array();		
				$prioList = $DBhandler->getPrioIDs();
				
				$categoryList = array();		
				$categoryList = $DBhandler->getCategoryIDs();

				$statusList = array();		
				$statusList = $DBhandler->getStatusIDs();
				
				$defaultGroupId = $mailboxSettings[8];
				$defaultPrioId = $mailboxSettings[9];				
				$defaultStatusId = $mailboxSettings[10];
				$defaultCategoryId = $mailboxSettings[11];		
								
			?>

			
			<div class='row'> 
				<div class='col-lg-4'> 
			        <div class='control-group' style='margin-bottom: 10px;'> 
			          <label for='level'>Grupp: </label> 
						<select name='group' class=\"form-control\"> 					
							<?php 							
								foreach($groupList as $value){ 
									
									$selected = ($defaultGroupId == $value) ? "selected" : "";
					
									echo "<option value=$value $selected>".$DBhandler->getGroupName($value)."</option>";					
								} 
							?>							
						</select>		
			        </div> 
				</div> 
			</div> 
			
			<div class='row'> 
				<div class='col-lg-4'> 
			        <div class='control-group' style='margin-bottom: 10px;'> 
			          <label for='level'>Kategori: </label> 
						<select name='category' class=\"form-control\"> 					
							<?php 							
								foreach($categoryList as $value){ 
									
									$selected2 = ($defaultCategoryId == $value) ? "selected" : "";
					
									echo "<option value=$value $selected2>".$DBhandler->getCategoryName($value)."</option>";					
								} 
							?>							
						</select>		
			        </div> 
				</div> 
			</div> 

			<div class='row'> 
				<div class='col-lg-4'> 
			        <div class='control-group' style='margin-bottom: 10px;'> 
			          <label for='level'>Status: </label> 
						<select name='status' class=\"form-control\"> 					
							<?php 							
								foreach($statusList as $value){ 
									
									$selected3 = ($defaultStatusId == $value) ? "selected" : "";
					
									echo "<option value=$value $selected3>".$DBhandler->getStatusName($value)."</option>";					
								} 
							?>							
						</select>		
			        </div> 
				</div> 
			</div> 
			
			<div class='row'> 
				<div class='col-lg-4'> 
			        <div class='control-group' style='margin-bottom: 10px;'> 
			          <label for='level'>Prioritet: </label> 
						<select name='prio' class=\"form-control\"> 					
							<?php 							
								foreach($prioList as $value){ 
									
									$selected4 = ($defaultPrioId == $value) ? "selected" : "";
					
									echo "<option value=$value $selected4>".$DBhandler->getPrioName($value)."</option>";					
								} 
							?>							
						</select>		
			        </div> 
				</div> 
			</div> 

			<hr>

			
			<div class="row" style="padding-top: 10px;">
				<div class="col-lg-6">            
					<button type="submit" class="btn btn-primary" name="update">Uppdatera</button>
				
<!--
					<?php if($updateSettings){ ?>                                        
						<button type="submit" class="btn btn-primary" name="update">Update settings</button>
					<?php }else{ ?>				
						<button type="submit" class="btn btn-primary" name="submit" id="saveSettings">Save settings</button>
					<?php } ?>				
-->
	
				</div> 
			</div>                                       
	        
		</form>      

	</div>


<!--  ---------------------------- help modal --------------------------------------- -->

		  <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		          <h4 class="modal-title">Information about email services</h4>
		        </div>
		        
		        	<form class="form-horizontal" method="post" action="mailbox_settings.php" id="block-validate">
				        <div class="modal-body">

							
							<h4>Where to find your e‑mail account information</h4>
							
							<p>
							Your e‑mail service provider should provide you with the information
							you need to sign in to your e‑mail account. If you don&rsquo;t have
							this information available, contact your e‑mail provider. Your e‑mail
							provider is typically your Internet service provider (ISP), but might
							also be your employer, school, or an independent provider that offers
							POP3 or IMAP e‑mail accounts.
							</p>
							<p>
							To set up your e‑mail account, you will need to provide the following
							information:
							</p>

							<li><strong>E‑mail server name</strong>. Mail is stored on your e‑mail provider's server
							until you download it. You'll need to know whether your e‑mail provider
							stores mail on a POP3 or IMAP server. 
							</li><br>
							<li>
							<strong>E‑mail/username</strong>. This is the name you use to sign in to the e‑mail
							server. For many e‑mail services, this will be your entire e‑mail
							address (such as someone@example.com), but some e‑mail services might
							use only the portion before the at sign (@), while others might assign
							a different ID for sign-in purposes.
							</li><br>
							<li>
							<strong>Password</strong>. This is the password you chose or were given when you created
							your e‑mail account.
							</li>			
							
							
							<h4>POP3, SMTP e‑mail server types</h4>
							
							<li><strong>Post Office Protocol 3 (POP3) servers</strong> hold incoming e‑mail messages until you check your e‑mail, 
							at which point they're transferred to your computer. POP3 is the most common account 
							type for personal e‑mail. Messages are typically deleted from the server when you check your e‑mail.</li><br>

							<li><strong>Internet Message Access Protocol (IMAP) servers</strong> let you work with e‑mail messages without downloading 
							them to your computer first. You can preview, delete, and organize messages directly on the e‑mail server, 
							and copies are stored on the server until you choose to delete them. IMAP is commonly used for business 
							e‑mail accounts.</li><br>
							
							<li><strong>SSL Secure Sockets Layer (SSL) technology</strong> is a standard for encrypted client/server network connections.
							If your ISP supports it, you can connect to the server via SSL, and all communication is transparently encrypted and secured.</li>
							
				        </div>
			        
		        <div class="modal-footer">
		          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        </div>
		      </div><!-- /.modal-content -->
		    </div><!-- /.modal-dialog -->
		  </div><!-- /.modal -->
		  
		</form>		  

<!--  ---------------------------- / help modal --------------------------------------- -->

<!--  ---------------------------- Email setup modal --------------------------------------- -->

	<form class="form-horizontal" method="post" action="admin.php" id="block-validate04">         				
	
	  <div class="modal fade" id="emailSetupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title">Epostinst&auml;llningar</h4>
	        </div>
	        <div class="modal-body">

                <div class="controls" id="ajaxContent04">							
						<!-- Ajax call here  -->

                </div>
	          
	        </div>

	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
			  <button class="btn btn-primary" type="submit" name="updateEmailSettings">Uppdatera</button>		          
	        </div>
	      </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	  </div><!-- /.modal -->
	  
	</form>
		
<!--  ---------------------------- /Email setup  modal --------------------------------------- -->

<!--  ---------------------------- password modal --------------------------------------- -->

<!--
		<form name="mailSettings" action="mailbox_settings.php" method="post">                

		  <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		          <h4 class="modal-title">Please enter the email password</h4>
		        </div>
		        
			        <div class="modal-body">

						<div class="row">
							<div class="col-lg-12">    
								<div class='alert alert-info'>The password will be saved in a session and destroyed when you log out.</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-lg-4">            	                                                                                
				                <div class="control-group" style='margin-bottom: 10px;'>
				                  <label for="mailbox">New password</label>
				                  <div class="controls">
									  <input type="password" class="form-control" id="mailboxPassword" name="mailboxPassword">	                  
				                  </div>
				                </div>                                        
							</div>
						</div>                                        			          
			        </div>
			        
		        <div class="modal-footer">
		          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		          <button class="btn btn-primary" type="submit" name="savePassword">Update</button>
		        </div>
		      </div>
		    </div>
		  </div>
		  
		</form>		  
-->
		  

<!--  ---------------------------- /password modal --------------------------------------- -->


<!--  ---------------------------- Test modal --------------------------------------- -->

<!--
		  <div class="modal fade" id="pleaseWaitDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		          <h4 class="modal-title">Update task</h4>
		        </div>
		        <div class="modal-body">


		            <div class="progress">
		                
		                 <div id="bar" class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"> </div>
		                
		            </div>

		          
		        </div>

		        <div class="modal-footer">
		          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		          <a href="#" class="btn btn-primary" id="updateTask">Update</a>
		        </div>
		      </div>
		    </div>
		  </div>
-->

<!--  ---------------------------- /Test modal --------------------------------------- -->


	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="assets/js/lib/jquery.mousewheel.js"></script>
	<script type="text/javascript" src="assets/js/lib/jquery.nicescroll.min.js"></script>	
	<script type="text/javascript" src="assets/js/lib/prettify.js"></script>		
	<script type="text/javascript" src="assets/js/lib/jquery.tablesorter.min.js"></script>
    <script type='text/javascript' src="assets/js/lib/jquery.validationEngine.js"></script>
    <script type='text/javascript' src="assets/js/lib/languages/jquery.validationEngine-en.js"></script>
    <script type='text/javascript' src="assets/js/lib/jquery.validate.min.js"></script>
    <script type="text/javascript" src="assets/js/lib/bootstrap-progressbar.min.js"></script>         
    <script type='text/javascript' src="assets/js/main.js"></script>
    <script type="text/javascript" src="assets/js/spin.min.js"></script> 
    
	<script type="text/javascript" src="assets/js/bootbox.min.js"></script>		    
    
	<script>		

		var opts = {
		  lines: 10, // The number of lines to draw
		  length: 20, // The length of each line
		  width: 10, // The line thickness
		  radius: 30, // The radius of the inner circle
		  corners: 0.6, // Corner roundness (0..1)
		  rotate: 11, // The rotation offset
		  direction: 1, // 1: clockwise, -1: counterclockwise
		  color: '#000', // #rgb or #rrggbb
		  speed: 2, // Rounds per second
		  trail: 56, // Afterglow percentage
		  shadow: false, // Whether to render a shadow
		  hwaccel: false, // Whether to use hardware acceleration
		  className: 'spinner', // The CSS class to assign to the spinner
		  zIndex: 2e9, // The z-index (defaults to 2000000000)
		  top: 'auto', // Top position relative to parent in px
		  left: 'auto' // Left position relative to parent in px
		};

		var target = document.getElementById('spin');
		var spinner = new Spinner(opts);

		/* ------------------------------------------------------*/

		$('#port').on("click", function(event) {
		
			var message; 
			
			message	= "<h3>Port number</h3>";
			message	+= "<ul><li>POP3 – port 110</li><li>IMAP – port 143</li><li>IMAP SSL (IMAPS) – port 993</li></ul>";

			bootbox.alert(message, function() {

			});
			

		});

		/* ---------------------------------------- */

		function emailSetupModal(){
		
			var emailSetupModal = "emailSetupModal";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({emailSetupModal: emailSetupModal}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent04").html(data);
		      } 
		    });
		}		    		    




		/* ------------------------------------------------------*/


/*
		function passwordModal() { 
		
			$('#passwordModal').modal('show');			
		
		}

		$('#passwordModal').on('shown.bs.modal', function () {
		    $('#mailboxPassword').focus();
		})
*/

		/* ------------------------------------------------------*/

/*
<?php
	if($showPasswordModal == true){
?>

        $(function() {
			$('#passwordModal').modal('show');	
        });

<?php
	}		
?>
*/
		
		/* ------------------------------------------------------*/

/*
<?php
	if($testConnection == true){
?>


        $(function() {
			testConnection(<?=$id ?>);	
        });


<?php
	}		
?>
*/

		/* ------------------------------------------------------*/

		$('#pathPrefix').on("click", function(event) {
		
			var message; 
			
			message	= "<h3>IMAP Path Prefix</h3>";
			message	+= "The IMAP path prefix is INBOX. Please be sure to enter this in all caps.<br><br>";			
			message	+= "The IMAP's path prefix is generally pre-configured on most third party mail clients and can be remedied by subscribing to the IMAP folders in question.";

			bootbox.alert(message, function() {

			});
			

		});		
		
		/* ------------------------------------------------------*/

/*
		// $.xhrPool and $.ajaxSetup are the solution
		$.xhrPool = [];
		$.xhrPool.abortAll = function() {
		    $(this).each(function(idx, jqXHR) {
		        jqXHR.abort();
		    });
		    $.xhrPool = [];
		};
		
		$.ajaxSetup({
		    beforeSend: function(jqXHR) {
		        $.xhrPool.push(jqXHR);
		    },
		    complete: function(jqXHR) {
		        var index = $.xhrPool.indexOf(jqXHR);
		        if (index > -1) {
		            $.xhrPool.splice(index, 1);
		        }
		    }
		});
		
		// Everything below this is only for the jsFiddle demo
		$('#cancelBtn').click(function() {
		
			console.log("Cancel");	
		    $.xhrPool.abortAll();
		});
*/

		/* ------------------------------------------------------*/

        $(function() {
            formValidation();
        });

		/* ------------------------------------------------------*/
        
		function testConnection() { 
		
			console.log('testConnection');	
			
			$("#message").html("");						
			$("#testResult").html("");						
			
			var testConnection = "testConnection"; 			            				

			spinner.spin(target);

			xhr = $.ajax({                                      
		      url: 'ajax.php', 
			  type: "POST",			      
			  timeout: 10000,
		      data: ({testConnection: testConnection}),             			                         
		      success: function(data) 
		      {
				  $("#testResult").html(data);
				  spinner.stop(); 
		      },
			  error: function(jqXHR, exception) {
                if (jqXHR.status === 0) {
                    console.log('No network');
                } else if (jqXHR.status == 404) {
					console.log('Not fount [Error: 400].'); 
                } else if (jqXHR.status == 500) {
					console.log('[Error: 500].');    
                }
                if (exception === 'parsererror') {
					console.log('parser error');                   
                } else if (exception === 'timeout') {
					console.log('timeout');
					xhr.abort();
					spinner.stop(); 	
					$("#testResult").html("<div class='alert alert-danger'>Got timeout! Please try again.</div>");				
                } else if (exception === 'abort') {
					console.log('abort'); 
                } else {
                    console.log('Undefined'); 
                }
			}		      
		       
		    });
		
		}
        
        
		/* ------------------------------------------------------*/        
		

/*
function animateScroll()
    {
    $('#bar').animate({
    width: '100%'
}, {
    duration: 5000,
    easing: 'linear',
    complete:function(){ $(this).css("width","0%");
   }
});
animateScroll();
}
*/


		function testConnection2() { 		


			/* 		animateScroll(); */
		
			
/* 			$('#bar').animate({ width: "100%" },10000);			 */

/*
			var w
			var speed = 100;
			var increment = (speed/100);
			for(var x = 0; x<speed; x++)
			{
			  setTimeout(doIncrement(increment),1000);
			}
*/
		
/*
			var progress = setInterval(function() {
			    var $bar = $('#bar');
			    
			    if ($bar.width()==400) {
			        clearInterval(progress);
			        $('.progress').removeClass('active');
			    } else {
			        $bar.width($bar.width()+40);
			    }
			    $bar.text($bar.width()/4 + "%");
			}, 800);
*/

		}
		
		/* ------------------------------------------------------*/        

/* 		----------------- LOG OUT SCRIPT -------------------		 */

		<?php include("include/sessionLogout.php"); ?>
		
/* 		----------------- / LOG OUT SCRIPT -------------------		 */
		
				
	</script>


	</body>
</html>
<?php
	
} 
?>