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

$groupID = $DBhandler->getGroupID($userID);	

$groupName = $DBhandler->getGroupName($groupID);	 

//Get level
// level 1=Admin 2=Handläggare 3=Koordinator 4=Kund

$level = $DBhandler->getLevel($email);

if($level != 2)
	header("Location: logout.php");								

// Get username from email
$parts = explode("@", $email);
$username = $parts[0];


if(isset($_POST['editTicket'])){

	$message = "";
	$error = false;
	
	if($_POST['status'] == 1){
		$message = "<div class='alert alert-danger'>Status m&aring;ste vara skild fr&aring;n <strong>Nytt</strong>.</div>";
		$error = true;
	}
	
	if($_POST['answer'] == ""){
		$message .= "<div class='alert alert-danger'>Svar till kunden m&aring;ste vara ifylld.</div>";
		$error = true;		
	}
	
	if(!$error){
		$data = array();
	
		$data[] = $_POST['id']; 
		$data[] = $_POST['prio']; 	        
		$data[] = $_POST['status']; 
		$data[] = $_POST['answer']; 
		$data[] = $_POST['log']; 		
		$data[] = $userID; 	
		$data[] = $_POST['subject']; 
		$data[] = $_POST['description']; 
					
		$DBhandler->updateTicket($data);			
	}

}


if(isset($_POST['updatePassword'])){

	$userID = $_POST['id'];         	
	$password = $_POST['newPassword'];         

	$DBhandler->updatePassword($userID,$password);

}

if(isset($_POST['updateUser'])){

	$data = array();
	
	$data[] = $_POST['id'];         
	$data[] = $_POST['name']; 
	$data[] = $_POST['efternamn']; 
	$data[] = $_POST['adress']; 	
	$data[] = $_POST['postdress']; 		
	$data[] = $_POST['ort']; 		
	$data[] = $_POST['telefonnr']; 
	$data[] = $_POST['mobilnr']; 
		
	// Check username if exists
	
	$DBhandler->updateUser2($data);

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
	
	<body onload="javascript:ListTickets()">
	
   <div class="container">

	   <!-- Static navbar -->
      <div class="navbar navbar-default">
        
        <div class="navbar-header">
          <span class="navbar-brand">Handl&auml;ggare. Gruppen <strong><?=$groupName ?></strong></span>          
        </div>

        
        <div class="navbar-collapse collapse">

			<ul class="nav navbar-nav">

            <li class="dropdown">
                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-user"></i> <?=$username ?> <i class="caret"></i></a>
                
                <ul class="dropdown-menu">
                    <li>
						<a data-toggle='modal' href='#editModal' onclick="editUserModal(<?=$userID ?>)">Uppdatera profilen</a>				
                    </li>                
                    <li>
						<a data-toggle='modal' href='#changePasswordModal' onclick="changePasswordModal(<?=$userID ?>)">&Auml;ndra l&ouml;senord</a>				
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
			<h4>&Auml;renden</h4>			
			<a class="btn btn-primary" onClick="window.location.reload()"><i class="glyphicon glyphicon-refresh"></i></a>
			<a class="btn btn-primary" onClick="createTicketsFromEmail()">Skapa &auml;renden fr&aring;n epost</a>
	    </div>
    
		</div>

		<table cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered" id="ticketsTable">
							
			<thead>
				<tr>
					<th>ID</th>												
					<th>Datum</th>
					<th>S&auml;ndaren</th>					
					<th>Rubrik</th>
					<th>Status</th>
					<th>Prioritet</th>																
					<th>Fil</th>						
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
					<th>ID</th>												
					<th>Datum</th>
					<th>S&auml;ndaren</th>										
					<th>Rubrik</th>
					<th>Status</th>
					<th>Prioritet</th>					
					<th>Fil</th>					
					<th>&Aring;tg&auml;rd</th>																		
				</tr>
			</tfoot>
	
		</table>
		
		<div id="spin" style="margin: 0 auto;"></div>		
		
		<div id="ajaxResult"></div>		
		<p class="text-muted"><?php echo $DBhandler->getSupportformVersion(1); ?></p>			


		<!-- Modals -->
		

<!--  ---------------------------- Edit ticket modal --------------------------------------- -->

	
	<form class="form-horizontal" method="post" action="servicemanager.php" enctype="multipart/form-data" id="block-validate">         				
	
		  <div class="modal fade" id="editTicketModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		          <h4 class="modal-title">Uppdatera &auml;rendet</h4>
		        </div>
		        
			        <div class="modal-body">
	
	                    <div class="controls" id="ajaxContent02">							
	
								<!-- Ajax call here  -->
	                    </div>
			        </div>

			        <div class="modal-footer">
			          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
			          <button class="btn btn-primary" type="submit" name="editTicket">Svara</button>
			        </div>
		        
		      </div><!-- /.modal-content -->
		    </div><!-- /.modal-dialog -->
		  </div><!-- /.modal -->
		  
	</form>
		
<!--  ---------------------------- /Edit ticket modal --------------------------------------- -->

<!--  ---------------------------- View ticket detail modal --------------------------------------- -->

	  <div class="modal fade" id="viewDetailModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title">Visa &auml;rende</h4>
	        </div>
	        <div class="modal-body">

                <div class="controls" id="ajaxContent01">							
						<!-- Ajax call here  -->
                </div>
	          
	        </div>

	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
	        </div>
	      </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	  </div><!-- /.modal -->
	  
<!--  ---------------------------- /View ticket detail modal --------------------------------------- -->

<!--  ---------------------------- View ticket history modal --------------------------------------- -->

	  <div class="modal fade" id="viewDetailModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title">Historik</h4>
	        </div>
	        <div class="modal-body">

                <div class="controls" id="ajaxContent05">							
						<!-- Ajax call here  -->
                </div>
	          
	        </div>

	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
	        </div>
	      </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	  </div><!-- /.modal -->
	  
<!--  ---------------------------- /View ticket history  modal --------------------------------------- -->


<!--  ---------------------------- Change password modal --------------------------------------- -->

	<form class="form-horizontal" method="post" action="servicemanager.php" enctype="multipart/form-data" id="block-validate02">         				
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
	  
<!--  ---------------------------- /Change password modal --------------------------------------- -->

<!--  ---------------------------- Update user modal --------------------------------------- -->

	<form class="form-horizontal" method="post" action="customer.php" id="block-validate02">         				
	
	  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title">Uppdatera profilen</h4>
	        </div>
	        <div class="modal-body">

                <div class="controls" id="ajaxContent04">							
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

		function editUserModal(userID){
		
			console.log("userID: "+userID);
		
			var editUserModal2 = "editUserModal2";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({editUserModal2: editUserModal2, userID: userID}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent04").html(data);
		      } 
		    });
		}		    		    

		/* ---------------------------------------- */

		function editTicketModal(ticketID){
		
			console.log("ticketID: "+ticketID);
			var editTicketModal = "editTicketModal";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({editTicketModal: editTicketModal, ticketID: ticketID}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent02").html(data);
		      } 
		    });
		}		    		    

		/* ---------------------------------------- */
				
		function viewDetailModal2(ticketID,groupID){
		
			console.log("ticketID: "+ticketID);
			console.log("groupID: "+groupID);
					
			var viewDetailModal2 = "viewDetailModal2";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({viewDetailModal2: viewDetailModal2, ticketID: ticketID, groupID: groupID}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent01").html(data);
		      } 
		    });
		}		    		    

		/* ---------------------------------------- */
				
		function viewDetailModal3(ticketID){
		
			console.log("ticketID: "+ticketID);
					
			var viewDetailModal3 = "viewDetailModal3";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({viewDetailModal3: viewDetailModal3, ticketID: ticketID}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent05").html(data);
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
								
		function createTicketsFromEmail(){
		
			spinner.spin(target);
		
			console.log("createTicketsFromEmail");
			
			createTicketsFromEmail = "createTicketsFromEmail";			
					
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({createTicketsFromEmail: createTicketsFromEmail}),             			                            
		      success: function(data) 
		      {
				  console.log(data);
				  spinner.stop(); 
				  
				  location.reload(true);
				  
				  
		      } 
		    });
		}		    		    




		/* ---------------------------------------- */
		
			function ListTickets() { 		
		
			spinner.spin(target);

			$('#ticketsTable').dataTable( {
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
				      aoData.push( { "name": "getTickets2", "value": "getTickets2" } );
				},    

				"aaSorting": [[ 0, "desc" ]],
				"aoColumns": [ null, null, null, null,null,null,null,
				
				  { 
				    "bSearchable": false,
				    "bSortable": false,
				    "mDataProp": null,
				    "fnRender": function (oObj) {
				    	var operString = "";

/* 				    	operString += " <a role='button' class='btn btn-danger btn-sm deleteUser' href='#' onclick=\"deleteUser('"+oObj.aData[0]+"')\"><i class='glyphicon glyphicon-remove'></i></a>";		        */
				    	operString += " <a data-toggle='modal' href='#viewDetailModal2' class='btn btn-primary btn-sm' onclick=\"viewDetailModal2('"+oObj.aData[0]+"',<?=$groupID ?>)\"><i class='glyphicon glyphicon-info-sign'></i></a>";				    	
				    	operString += " <a data-toggle='modal' href='#viewDetailModal3' class='btn btn-primary btn-sm' onclick=\"viewDetailModal3('"+oObj.aData[0]+"')\"><i class='glyphicon glyphicon-zoom-in'></i></a>";				    					    	
				    	operString += " <a data-toggle='modal' href='#editTicketModal' class='btn btn-primary btn-sm' onclick=\"editTicketModal('"+oObj.aData[0]+"')\"><i class='glyphicon glyphicon-pencil'></i></a>";				    					    	
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