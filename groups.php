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

if(isset($_POST['saveGroup'])){

	$groupName = $_POST['groupname']; 
	
	$DBhandler->addGroup($groupName);			

}


if(isset($_POST['updateGroup'])){

	$groupID = $_POST['id'];         
	$groupName = $_POST['groupname'];         
		
	// Check username if exists
	
	if(!$DBhandler->updateGroup($groupID,$groupName));

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
	
	<body onload="javascript:ListGroups()">
	
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
                      
<!--
            <li class="dropdown">
                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-user"></i> <?=$username ?> <i class="caret"></i></a>
                
                <ul class="dropdown-menu">
                    <li>
                        <a tabindex="-1" href="profile.php">Profile</a>
                    </li>
                </ul>
            </li>
-->

		</ul>

          <ul class="nav navbar-nav navbar-right">
            <li><a href="logout.php">Logga ut</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      
      </div><!--/. Static navbar -->	
	
		<div class="row" style='margin-bottom: 20px;'>						
		


<!--
			<div class="col-sm-12">
				<a href='' class='btn btn-primary' disabled="disabled">Anv&auml;ndare</a>
				<a href='' class='btn btn-primary'>Grupper</a>
				<a href='' class='btn btn-primary'>Status</a>
				<a href='' class='btn btn-primary'>Prioritet</a>
				<a href='' class='btn btn-primary'>Kategorier</a>			
		    </div>
-->
		
		    <div class="col-sm-4">
				<div id="message"> <?=$message ?></div>                                     
		    
		    	<h4>Hantera grupper</h4>
				<a data-toggle='modal' href='#createGroupModal' class='btn btn-primary' onclick="createGroupModal()">Skapa en ny grupp</a>		
										
		    </div>
		    
		</div>

		<table cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered" id="usersTable">
							
			<thead>
				<tr>
					<th>id</th>												
					<th>Grupp</th>
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
					<th>Grupp</th>					
					<th>&Aring;tg&auml;rd</th>																		
				</tr>
			</tfoot>
	
		</table>
		
		<div id="spin" style="margin: 0 auto;"></div>		
		
		<div id="ajaxResult"></div>		
		<p class="text-muted"><?php echo $DBhandler->getSupportformVersion(1); ?></p>			


		<!-- Modals -->
		

<!--  ---------------------------- Create group modal --------------------------------------- -->

<!-- 	<form action="#" id="createUserForm" method="post">                 -->

<!-- 	<form class="form-horizontal" method="post" action="index.php" id="createUserForm">         		 -->
	
	<form class="form-horizontal" method="post" action="groups.php" id="block-validate">         				
	
		  <div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		          <h4 class="modal-title">Skapa en ny grupp</h4>
		        </div>
		        
			        <div class="modal-body">
	
	                    <div class="controls" id="ajaxContent02">							
	
								<!-- Ajax call here  -->
	                    </div>
			        </div>

			        <div class="modal-footer">
			          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
			          <button class="btn btn-primary" type="submit" name="saveGroup">Spara</button>
			        </div>
		        
		      </div><!-- /.modal-content -->
		    </div><!-- /.modal-dialog -->
		  </div><!-- /.modal -->
		  
	</form>
		
<!--  ---------------------------- /Create group modal --------------------------------------- -->

<!--  ---------------------------- Update user modal --------------------------------------- -->

	<form class="form-horizontal" method="post" action="groups.php" id="block-validate02">         				
	
	  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title">Uppdatera grupp</h4>
	        </div>
	        <div class="modal-body">

                <div class="controls" id="ajaxContent01">							
						<!-- Ajax call here  -->
                </div>
	          
	        </div>

	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">St&auml;ng</button>
			  <button class="btn btn-primary" type="submit" name="updateGroup">Uppdatera</button>		          
	        </div>
	      </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	  </div><!-- /.modal -->
	  
	</form>
		
<!--  ---------------------------- /Update user modal --------------------------------------- -->

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

		function createGroupModal(){
		
			var createGroupModal = "createGroupModal";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({createGroupModal: createGroupModal}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent02").html(data);
		      } 
		    });
		}		   
		
		/* ---------------------------------------- */
				
		function editGroupModal(groupID){
		
			console.log("groupID: "+groupID);
		
			var editGroupModal = "editGroupModal";
				
			$.ajax({                              
		      url: 'ajax.php', 
			  type: "POST",			      
		      data: ({editGroupModal: editGroupModal, groupID: groupID}),             			                            
		      success: function(data) 
		      {
				  $("#ajaxContent01").html(data);
		      } 
		    });
		}		    		    

		/* ---------------------------------------- */
					
		function deleteGroup(groupID) { 
		
		    bootbox.confirm("Vill du verkligen radera gruppen?", $.proxy(function(result) {
		        if (result) {

				console.log("groupID: "+groupID);				            							

				var deleteGroup = "deleteGroup";
			
				$.ajax({                                      
			      url: 'ajax.php', 
				  type: "POST",		
				  async: "false",	      
			      data: ({deleteGroup: deleteGroup, groupID: groupID}),             			                         
			      success: function(data) 
			      {
					  ListGroups();	
			      } 
			    });					
					
					
		        }
		    }, this));
		
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
		
		function ListGroups() { 		
		
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
				      aoData.push( { "name": "getGroups", "value": "getGroups" } );
				},    

				"aaSorting": [[ 0, "desc" ]],
				"aoColumns": [ {"bVisible": false}, null, 
				
				  { 
				    "bSearchable": false,
				    "bSortable": false,
				    "mDataProp": null,
				    "fnRender": function (oObj) {
				    	var operString = "";
				    	operString += " <a role='button' class='btn btn-danger btn-sm deleteGroup' href='#' onclick=\"deleteGroup('"+oObj.aData[0]+"')\"><i class='glyphicon glyphicon-remove'></i></a>";		       
				    	operString += " <a data-toggle='modal' href='#editModal' class='btn btn-primary btn-sm' onclick=\"editGroupModal('"+oObj.aData[0]+"')\"><i class='glyphicon glyphicon-pencil'></i></a>";
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