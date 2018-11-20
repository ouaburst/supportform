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
$userID = $DBhandler->getUserId($email);

$_SESSION['userID'] = $userID;

$message = "";

//Get level
// level 1=Admin 2=Handläggare 3=Koordinator 4=Kund

$level = $DBhandler->getLevel($email);

if($level != 1)
	header("Location: logout.php");								

// Get username from email
$parts = explode("@", $email);
$username = $parts[0];

/*
$activeTabEmails = "<li class='active'>";
$activeTabTasks = "<li>";
*/

if(isset($_POST['saveUser'])){

	$data = array();
    
    // Admin group id = 1
    if($_POST['level']==1)
		$_POST['group'] = 1; 

	// Kund group id = 2
    if($_POST['level']==4)
		$_POST['group'] = 2; 
        
	$data[] = $_POST['username']; 
	$data[] = $_POST['email']; 
	$data[] = $_POST['password']; 	
	$data[] = $_POST['level']; 		
	$data[] = $_POST['group']; 		

	$data[] = $_POST['name']; 
	$data[] = $_POST['efternamn']; 
	$data[] = $_POST['adress']; 	
	$data[] = $_POST['postdress']; 		
	$data[] = $_POST['ort']; 		
	$data[] = $_POST['telefonnr']; 
	$data[] = $_POST['mobilnr']; 
	$data[] = $_POST['signatur']; 	
		
	// Check username if exists

	if($DBhandler->checkUserName($_POST['username']))
		$message = "<div class='alert alert-danger'>Anv&auml;ndaren finns redan i databasen.<br>V&auml;nligen v&auml;lj en annan.</div>";
	else if($DBhandler->checkEmail($_POST['email']))
		$message = "<div class='alert alert-danger'>Epostadressen finns redan i databasen.<br>V&auml;nligen v&auml;lj en annan.</div>";
	else	
		$DBhandler->addUser($data);			

}


if(isset($_POST['updateUser'])){

	$data = array();
	
	$data[] = $_POST['id'];         
	$data[] = $_POST['username']; 
	$data[] = $_POST['email']; 
	
	if(!isset($_POST['level']))
		$data[] = 1;	// Admin level = 1;
	else
		$data[] = $_POST['level']; 			
	
	if(!isset($_POST['group']))
		$data[] = 1;	// Admin group = 1;
	else
		$data[] = $_POST['group']; 		

	$data[] = $_POST['name']; 
	$data[] = $_POST['efternamn']; 
	$data[] = $_POST['adress']; 	
	$data[] = $_POST['postdress']; 		
	$data[] = $_POST['ort']; 		
	$data[] = $_POST['telefonnr']; 
	$data[] = $_POST['mobilnr']; 
		
	// Check username if exists

	if(!$DBhandler->updateUser($data))
		$message = "<div class='alert alert-danger'>Epostadressen finns redan i databasen.<br>V&auml;nligen v&auml;lj en annan.</div>";

}

if(isset($_POST['updatePassword'])){

	$userID = $_POST['id'];         	
	$password = $_POST['newPassword'];         
		
	$DBhandler->updatePassword($userID,$password);
}

if(isset($_POST['updateEmailSettings'])){

	// Email for customer

	$data = array();

	$data[] = $_POST['id'];         	
	$data[] = $_POST['email'];         
	$data[] = $_POST['emailSubject'];         
	$data[] = $_POST['emailBody'];       
	$data[] = $_POST['senderName']; 		
	  
/* 	$data[] = $_POST['emailFooter'];          */

	$DBhandler->updateEmailSettings($data);

	// Email for tickets
	
	$data = array();

	$data[] = $_POST['id2'];         	
	$data[] = $_POST['email'];         
	$data[] = $_POST['emailSubject2'];         
	$data[] = $_POST['emailBody2'];  
	$data[] = $_POST['senderName']; 			       
/* 	$data[] = $_POST['emailFooter2'];          */

	$DBhandler->updateEmailSettings($data);


	$data = array();

	$data[] = $_POST['id3'];         	
	$data[] = $_POST['email'];         
	$data[] = $_POST['emailSubject3'];         
	$data[] = $_POST['emailBody3']; 
	$data[] = $_POST['senderName']; 			        
/* 	$data[] = $_POST['emailFooter2'];          */

	$DBhandler->updateEmailSettings($data);


	$data = array();

	$data[] = $_POST['id4'];         	
	$data[] = $_POST['email'];         
	$data[] = $_POST['emailSubject4'];         
	$data[] = $_POST['emailBody4'];
	$data[] = $_POST['senderName']; 			         
/* 	$data[] = $_POST['emailFooter2'];          */

	$DBhandler->updateEmailSettings($data);
	
}




?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Supportform V3">
		<meta name="author" content="obkonsult.com">
		<title>Supporform V2</title>
		<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">
<!-- 		<link rel="stylesheet" href="bootstrap/3.0.0/css/bootstrap.min.css"> -->
		<link rel="stylesheet" href="assets/css/datatables.css">

        <link rel="stylesheet" href="assets/css/datepicker.css" />
        
		<link rel="shortcut icon" href="http://webmail2task.com/admin2/assets/ico/favicon.ico"> 
        	
	</head>
	
<!-- 	<body> -->
	
	<body onload="javascript:ListUsers()">
	
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
	
		<div class="row" style='margin-bottom: 20px;'>						
		
		    <div class="col-sm-4">
				<div id="message"> <?=$message ?></div>                                     
		    
		    	<h4>Hantera anv&auml;ndare</h4>				
				<a data-toggle='modal' href='#createUserModal' class='btn btn-primary' onclick="createUserModal()">Skapa en ny anv&auml;ndare</a>	
		    </div>
		    
		</div>

		<table cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered" id="usersTable">
							
			<thead>
				<tr>
					<th>id</th>												
					<th>Anv&auml;nadre</th>
					<th>Epost</th>
					<th>Grupp</th>											
					<th>Registrerad</th>																					
					<th>Beh&ouml;righet</th>									
					<th>&Aring;tg&auml;rd</th>																											
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="5" class="dataTables_empty">Loading data from server</td>																
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th>id</th>												
					<th>Anv&auml;nadre</th>
					<th>Epost</th>
					<th>Grupp</th>											
					<th>Registrerad</th>																					
					<th>Beh&ouml;righet</th>	
					<th>&Aring;tg&auml;rd</th>																		
				</tr>
			</tfoot>
	
		</table>
		
		<div id="spin" style="margin: 0 auto;"></div>		
		
		<div id="ajaxResult"></div>	
		<p class="text-muted"><?php echo $DBhandler->getSupportformVersion(1); ?></p>	

		<!-- Modals -->
		

<!--  ---------------------------- Create user modal --------------------------------------- -->

<!-- 	<form action="#" id="createUserForm" method="post">                 -->

<!-- 	<form class="form-horizontal" method="post" action="admin.php" id="createUserForm">         		 -->
	
	<form class="form-horizontal" method="post" action="admin.php" id="block-validate">         				
	
		  <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		          <h4 class="modal-title">Skapa en ny anv&auml;ndare</h4>
		        </div>
		        
			        <div class="modal-body">
	
	                    <div class="controls" id="ajaxContent02">							
	
								<!-- Ajax call here  -->
	                    </div>
			        </div>

			        <div class="modal-footer">
			          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
			          <button class="btn btn-primary" type="submit" name="saveUser">Spara</button>
			        </div>
		        
		      </div><!-- /.modal-content -->
		    </div><!-- /.modal-dialog -->
		  </div><!-- /.modal -->
		  
	</form>
		
<!--  ---------------------------- /Create user modal --------------------------------------- -->

<!--  ---------------------------- Update user modal --------------------------------------- -->

	<form class="form-horizontal" method="post" action="admin.php" id="block-validate02">         				
	
	  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title">Uppdatera anv&auml;ndaren</h4>
	        </div>
	        <div class="modal-body">

                <div class="controls" id="ajaxContent01">							
						<!-- Ajax call here  -->
                </div>
	          
	        </div>

	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
			  <button class="btn btn-primary" type="submit" name="updateUser">Uppdatera</button>		          
	        </div>
	      </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	  </div><!-- /.modal -->
	  
	</form>
		
<!--  ---------------------------- /Update user modal --------------------------------------- -->

<!--  ---------------------------- Change password modal --------------------------------------- -->

	<form class="form-horizontal" method="post" action="admin.php" id="block-validate03">         				
	
	  <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title">&Auml;ndra l&ouml;senord</h4>
	        </div>
	        <div class="modal-body">

                <div class="controls" id="ajaxContent03">							
						<!-- Ajax call here  -->

                </div>
	          
	        </div>

	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
			  <button class="btn btn-primary" type="submit" name="updatePassword">Uppdatera</button>		          
	        </div>
	      </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	  </div><!-- /.modal -->
	  
	</form>
		
<!--  ---------------------------- /Change password  modal --------------------------------------- -->

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

        
        <!-- /#EditModal -->

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>		
		<script src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.9.4/jquery.dataTables.min.js"></script>
		<script src="assets/js/datatables.js"></script>
		
        <script type="text/javascript" src="assets/js/lib/jquery.tablesorter.min.js"></script>		
        <script type="text/javascript" src="assets/js/lib/bootstrap-datepicker.js"></script> 

	    <script type='text/javascript' src="assets/js/lib/jquery.validationEngine.js"></script>
	    <script type='text/javascript' src="assets/js/lib/jquery.validate.min.js"></script>
	    <script type='text/javascript' src="assets/js/lib/languages/messages_sv.js"></script>        	                    
        
        <script type="text/javascript" src="assets/js/main.js"></script>     
				
		<script type="text/javascript" src="assets/js/spin.min.js"></script>
		
		<script type="text/javascript" src="assets/js/bootbox.min.js"></script>		
		
		
		<script>		
	        $(function() {
	            formValidation();
	        });
	                
		</script>
		
		
		<script type="text/javascript">

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

		/* ---------------------------------------- */

		function createUserModal(){
		
			var createUserModal = "createUserModal";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({createUserModal: createUserModal}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent02").html(data);
		      } 
		    });
		}		    		    

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

		/* ---------------------------------------- */
						
		function editUserModal(userID){
		
			console.log("userID: "+userID);
		
			var editUserModal = "editUserModal";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({editUserModal: editUserModal, userID: userID}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent01").html(data);
		      } 
		    });
		}		    		    

		/* ---------------------------------------- */
				
		function changePasswordModal(userID){
		
			console.log("userID: "+userID);
		
			var changePasswordModal = "changePasswordModal";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({changePasswordModal: changePasswordModal, userID: userID}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent03").html(data);
		      } 
		    });
		}		    		    


		/* ---------------------------------------- */		
	
		function deleteUser(userID) { 
		
		    bootbox.confirm("Vill du verkligen radera kontot?", $.proxy(function(result) {
		        if (result) {

				console.log("userID: "+userID);				            							

				var deleteUser = "deleteUser";
			
				$.ajax({                                      
			      url: 'ajax.php', 
				  type: "POST",		
				  async: "false",	      
			      data: ({deleteUser: deleteUser, userID: userID}),             			                         
			      success: function(data) 
			      {
					  ListUsers();	
			      } 
			    });					
					
					
		        }
		    }, this));
		
		}

		/* ---------------------------------------- */

		function getUserLevel(sel) {
		
			var value = sel.options[sel.selectedIndex].value;  

			console.log("value: "+value);				            										
						
			if(value == 1 || value == 4)
				$("#groupSelection").hide();			      					
			if(value == 2)			
				$("#groupSelection").show();			      				
				
		}

		/* ---------------------------------------- */
		
		function ListUsers() { 		
		
			spinner.spin(target);

			$('#usersTable').dataTable( {
				"bDestroy": true,				
				"bProcessing": true,
				"sAjaxSource": "ajax.php",
				"sPaginationType": "bs_full",
				"bAutoWidth": false,
				
				"fnServerData": function ( sUrl, aoData, fnCallback, oSettings ) {
						oSettings.jqXHR = $.ajax( {
							"url":  sUrl,
							"data": aoData,
							"success": function (json) {
							
								spinner.stop();
								
								if ( json.sError ) {
									oSettings.oApi._fnLog( oSettings, 0, json.sError );
								}
								
								$(oSettings.oInstance).trigger('xhr', [oSettings, json]);
								fnCallback( json );
							},
							"dataType": "json",
							"cache": false,
							"type": oSettings.sServerMethod,
							"error": function (xhr, error, thrown) {
								
								spinner.stop();
				
								if ( error == "parsererror" ) {
									oSettings.oApi._fnLog( oSettings, 0, "DataTables warning: JSON data from "+
										"server could not be parsed. This is caused by a JSON formatting error." );
								}
							}
						} );
					},

				"sServerMethod": "POST",
				"fnServerParams": function ( aoData ) {
				      aoData.push( { "name": "getUsers", "value": "getUsers" } );
				},    

				"aaSorting": [[ 0, "desc" ]],
				"aoColumns": [ {"bVisible": false}, null, null, null,{"bVisible": false}, null,
				
				  { 
				    "bSearchable": false,
				    "bSortable": false,
				    "mDataProp": null,
				    "fnRender": function (oObj) {
				    	var operString = "";
				    	
				    	if(oObj.aData[0] != 1 && oObj.aData[0] != 2){				    	
					    	operString += " <a role='button' class='btn btn-danger btn-sm deleteUser' href='#' onclick=\"deleteUser('"+oObj.aData[0]+"')\"><i class='glyphicon glyphicon-remove'></i></a>";		       
					    }
					    else
					    	operString += "";
					    	
							operString += " <a data-toggle='modal' href='#editModal' class='btn btn-primary btn-sm' onclick=\"editUserModal('"+oObj.aData[0]+"')\"><i class='glyphicon glyphicon-pencil'></i></a>";

							if(oObj.aData[0] != 2){				    	
					    		operString += " <a data-toggle='modal' href='#changePasswordModal' class='btn btn-primary btn-sm' onclick=\"changePasswordModal('"+oObj.aData[0]+"')\"><i class='glyphicon glyphicon-lock'></i></a>";				    	
							}
												    		
							return operString;
				     }
				  }
				  
				]
			} );
		
		
		}
		
/* 		----------------- LOG OUT SCRIPT -------------------		 */

		<?php include("include/sessionLogout.php"); ?>
		
/* 		----------------- / LOG OUT SCRIPT -------------------		 */
		
		
		</script>
	</body>
</html>
<?php
	
} 
?>