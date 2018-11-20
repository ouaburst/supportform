<?php
/*
 * Product: Supportform 
 * Version: 2.0
 * Author: Oualid Burstrom
 * Email: ob@obkonsult.com 
 * Data: 2014-01-31
 * 
 */


include('Mailer.php');

/* 
 * Class handles database connection, providing methods for common functions
 */
class DBHandler
{
	private $dbUrl;
	private $dbName;
	private $dbUser;
	private $dbPass;
	
	function __construct($dbUrl  = '', $dbName  = '', $dbUser  = '', $dbPass = '') //Database connection parameters are required
	{
		$this->dbUrl = $dbUrl;
		$this->dbName = $dbName;
		$this->dbUser = $dbUser;
		$this->dbPass = $dbPass;
		

		$this->mysqli = new mysqli($dbUrl, $dbUser, $dbPass, $dbName);
		
		/* check connection */
		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}

	}

/* ---------------------- Supportorm V2 ------------------------ */



	function getUsers(){
	
		$query = "select\n"
		    . "	`spform_users`.`id`,\n"
		    . "	`spform_users`.`username`,\n"
		    . "	`spform_users`.`email`,\n"
		    . "	`spform_users`.`date_reg`,\n"
		    . "	`spform_users`.`level`,\n"
		    . "	`spform_groups`.`group_name` \n"
		    . "from\n"
		    . "	`spform_users` `spform_users` \n"
		    . "	 inner join `spform_groups` `spform_groups` \n"
		    . "	 on `spform_users`.`groups` = `spform_groups`.`id`";
				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$output = array( "aaData" => array());
		
		while($row = $rs->fetch_assoc()){
			
			$content = array();	
			
			$content[] = $row['id'];		
			$content[] = $row['username'];
			$content[] = $row['email'];
			$content[] = $row['group_name'];	
			$content[] = $row['date_reg'];

			if($row['level'] == 1)
				$textLevel = "Admin";
			if($row['level'] == 2)
				$textLevel = utf8_encode("Handläggare");
			if($row['level'] == 3)
				$textLevel = "Koordinator";
			if($row['level'] == 4)
				$textLevel = "Kund";
									
			$content[] = $textLevel;
			$content[] = "";						
									
			$output['aaData'][] = $content;
			
		}

		$rs->close();		
				
		return $output;
		
	}

	function deleteUser($id){	
	
		$query = "DELETE FROM spform_users WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();
		
		$stmt->close();	
		
		return $result;				
		
	}
	
	
	function createUserModal(){
	
		$htmlString = "";
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Av&auml;ndarnamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='username' id='username' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>E-post *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='email' id='email' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>L&ouml;senord *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='password' name='password' id='password' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label for='level'>Beh&ouml;righet *</label> \n"; 
		$htmlString .= "			<select name='level' class=\"form-control\" id=\"comboA\" onchange=\"getUserLevel(this)\">\n"; 
/* 		$htmlString .= "  				<option value=1>Admin</option>\n";  */
		$htmlString .= "  				<option value=2>Handl&auml;ggare</option>\n"; 
/* 		$htmlString .= "  				<option value=3>Koordinator</option>\n";  */
		$htmlString .= "  				<option value=4 selected>Kund</option>\n"; 
		$htmlString .= "			</select>\n";		
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div>\n"; 

		$group_list = array();		
		$group_list = $this->getGroupIDs();
		
		

		$htmlString .= "<div id='groupSelection' style='display: none;'> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label for='level'>Grupp *</label> \n"; 
		$htmlString .= "			<select name='group' class=\"form-control\">\n"; 
		
		foreach($group_list as $value){
		
			$htmlString .= "<option value=$value>".$this->getGroupName($value)."</option>\n"; 
		
		}
				
		$htmlString .= "			</select>\n";		
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div>\n"; 
		$htmlString .= "<hr>\n"; 		
		
		$htmlString .= "</div>\n"; 





		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Namn</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='name' id='name' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Efternamn</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='efternamn' id='efternamn' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" >Adress</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='adress' id='adress' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Postdress</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='postdress' id='postdress' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Ort</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='ort' id='ort' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Telefonnr</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='telefonnr' id='telefonnr' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Mobilnr</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='mobilnr' id='mobilnr' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

/*
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Signatur</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <textarea class='form-control' rows='3' name='signatur' id='signatur'></textarea> \n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 
*/
		return $htmlString;		
				
	}	
	
	function emailSetupModal(){	
	
		$htmlString = "";
		
		// =================================== Mail1 ====================================
			
		$query = "select * FROM spform_email WHERE id = 1";
		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));		
		$rs->data_seek(0);
		
		while($row = $rs->fetch_assoc()){
			
			$id = $row['id'];		
			$sender = $row['sender'];
			$subject = $row['subject'];
			$body = $row['body'];												
			$footer = $row['footer'];
			$senderName = $row['sender_name'];			
		}
		$rs->close();		
				
		$htmlString .= "<input type='hidden' name='id' value=$id>\n"; 				

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>S&auml;ndarens namn:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='senderName' id='senderName' value='$senderName' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div>\n"; 

		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>Ert f&ouml;retags epostadress:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='email' id='email' value='$sender' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div><hr>\n"; 
		
		$htmlString .= "			 <div class='row'>\n"; 
		$htmlString .= "				 <div class='col-lg-8'>    \n"; 
		$htmlString .= "			  		<div class='alert alert-info'>\n"; 
		$htmlString .= "				  		Variablerna mellan '{' och '}' &auml;r reserverade.<br>De h&auml;mtas fr&aring;n databasen n&auml;r epost skickas.\n"; 
		$htmlString .= "			  		</div>\n"; 
		$htmlString .= "				</div>\n"; 
		$htmlString .= "			</div>\n"; 		
		
		
		
		$htmlString .= "<strong>Bekr&auml;ftelse till kunden n&auml;r ett konto har skapats:</strong><br>\n";	
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>Rubrik:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='emailSubject' id='emailSubject' value='$subject'>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Inneh&aring;ll:</label><br> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <textarea class='form-control' rows='5' name='emailBody' id='emailBody'>$body</textarea> \n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div><hr> \n"; 


		// =================================== Mail2 ====================================
			
		$query = "select * FROM spform_email WHERE id = 2";
		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));		
		$rs->data_seek(0);
		
		while($row = $rs->fetch_assoc()){
			
			$id = $row['id'];		
			$sender = $row['sender'];
			$subject = $row['subject'];
			$body = $row['body'];												
			$footer = $row['footer'];
		}
		$rs->close();		
		
		$htmlString .= "<input type='hidden' name='id2' value=$id>\n"; 				
		
		$htmlString .= "<strong>Bekr&auml;ftelse till kunden n&auml;r &auml;rendet har mottagits:</strong>\n";
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>Rubrik:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='emailSubject2' id='emailSubject2' value='$subject'>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Inneh&aring;ll:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <textarea class='form-control' rows='5' name='emailBody2' id='emailBody2'>$body</textarea> \n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div><hr> \n"; 


		// =================================== Mail3 ====================================
			
		$query = "select * FROM spform_email WHERE id = 3";
		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));		
		$rs->data_seek(0);
		
		while($row = $rs->fetch_assoc()){
			
			$id = $row['id'];		
			$sender = $row['sender'];
			$subject = $row['subject'];
			$body = $row['body'];												
			$footer = $row['footer'];
		}
		$rs->close();		
		
		$htmlString .= "<input type='hidden' name='id3' value=$id>\n"; 				
		
		$htmlString .= "<strong>Svar fr&aring;n handl&auml;ggaren till kunden:</strong>\n";
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>Rubrik:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='emailSubject3' id='emailSubject3' value='$subject'>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Inneh&aring;ll:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <textarea class='form-control' rows='5' name='emailBody3' id='emailBody3'>$body</textarea> \n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div><hr> \n"; 



		// =================================== Mail4 ====================================
			
		$query = "select * FROM spform_email WHERE id = 4";
		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));		
		$rs->data_seek(0);
		
		while($row = $rs->fetch_assoc()){
			
			$id = $row['id'];		
			$sender = $row['sender'];
			$subject = $row['subject'];
			$body = $row['body'];												
			$footer = $row['footer'];
		}
		$rs->close();		
		
		$htmlString .= "<input type='hidden' name='id4' value=$id>\n"; 				
		
		$htmlString .= "<strong>Epost som skickas till handl&auml;ggaren n&auml;r kunden skapar ett &auml;rende:</strong>\n";
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>Rubrik:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='emailSubject4' id='emailSubject4' value='$subject'>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Inneh&aring;ll:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <textarea class='form-control' rows='5' name='emailBody4' id='emailBody4'>$body</textarea> \n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		
		return $htmlString;				
	
	}
	
	
	
	function createTicketModal($userID){
	
		$query = "SELECT username,email From spform_users WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $userID);
		
		$stmt->execute();
	
		$stmt->bind_result($username,$email);
	
		$stmt->fetch();
		
		$stmt->close();							
	
		$today = date('Y-m-d');	
	
		$htmlString = "";
		
		$htmlString .= "<input type='hidden' name='date' value=$today>\n"; 		
		$htmlString .= "<input type='hidden' name='userid' value=$userID>\n"; 						
		$htmlString .= "<input type='hidden' name='username' value=$username>\n"; 		
		$htmlString .= "<input type='hidden' name='email' value=$email>\n"; 						
		$htmlString .= "<input type='hidden' name='status' value=1>\n"; 								
/*
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Datum</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='date' id='date' value=$today disabled>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>Anv&auml;ndare</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='username' id='username' value=$username disabled>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>E-post </label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='email' id='email' value=$email disabled>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 
*/


		$prio_list = array();		
		$prio_list = $this->getPrioIDs();

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label for='level'>Prioritet</label> \n"; 
		$htmlString .= "			<select name='prio' class=\"form-control\">\n"; 
		
		foreach($prio_list as $value){
		
			if($value==2)	// 2 = Normal
				$selected = "selected";
			else
				$selected = "";
				
			$htmlString .= "<option value=$value $selected>".$this->getPrioName($value)."</option>\n"; 
		
		}
				
		$htmlString .= "			</select>\n";		
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div>\n"; 

/*
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Status</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='status' id='status' value='Nytt' disabled>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 
*/




		$group_list = array();		
		$group_list = $this->getGroupIDs();

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label for='level'>Grupp</label> \n"; 
		$htmlString .= "			<select name='group' class=\"form-control\">\n"; 
		
		foreach($group_list as $value){
		
			$htmlString .= "<option value=$value>".$this->getGroupName($value)."</option>\n"; 
		
		}				
		$htmlString .= "			</select>\n";		
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div>\n"; 
		
		
		

		$category_list = array();		
		$category_list = $this->getCategoryIDs();

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label for='level'>Kategori</label> \n"; 
		$htmlString .= "			<select name='category' class=\"form-control\">\n"; 
		
		foreach($category_list as $value){
		
			$htmlString .= "<option value=$value>".$this->getCategoryName($value)."</option>\n"; 
		
		}				
		$htmlString .= "			</select>\n";		
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div>\n"; 
		
		

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Rubrik</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='subject' id='subject' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Beskrivning</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <textarea class='form-control' rows='5' name='description' id='description'></textarea> \n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Ladda upp fil (Max storlek 16M)</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <input class='form-control' type='file' name='file' id='file' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		return $htmlString;		
				
	}		


	function viewDetailModal($ticketID,$userID){
	
		$htmlString = "";
	
		$query = "select
			`spform_tickets`.`id`,
			`spform_tickets`.`date`,
			`spform_prio`.`prio`,
			`spform_status`.`status`,
			`spform_groups`.`group_name`,
			`spform_category`.`category`,
			`spform_tickets`.`subject`,
			`spform_tickets`.`description`,
			`spform_tickets`.`file`,
			`spform_tickets`.`answer` 
		from
			`spform_tickets` `spform_tickets` 
				inner join `spform_category` `spform_category` 
				on `spform_tickets`.`category_id` = `spform_category`.`id` 
					inner join `spform_groups` `spform_groups` 
					on `spform_tickets`.`group_id` = `spform_groups`.`id` 
						inner join `spform_status` `spform_status` 
						on `spform_tickets`.`status_id` = `spform_status`.`id` 
							inner join `spform_prio` `spform_prio` 
							on `spform_tickets`.`prio_id` = `spform_prio`.`id`										
							WHERE `spform_tickets`.`id` = ? AND `spform_tickets`.`user_id` = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('ii', $ticketID,$userID);
		
		$stmt->execute();
	
		$stmt->bind_result($id,$date,$prio,$status,$group,$category,$subject,$description,$file,$answer);
	
		$stmt->fetch();
		
		$stmt->close();	
		
		
/* 		$htmlString .= 	"<div class='row'><div class='col-sm-4'>&Auml;rendeID: $id</div></div>"; */
		$htmlString .= "<strong>&Auml;rende #:</strong> $id<hr>";
		$htmlString .= "<strong>Datum:</strong> $date<hr>";								
		$htmlString .= "<strong>Prioritet:</strong> $prio<hr>";		
		$htmlString .= "<strong>Status:</strong> $status<hr>";
		$htmlString .= "<strong>Skickat till gruppen:</strong> $group<hr>";
		$htmlString .= "<strong>Kategori:</strong> $category<hr>";
		$htmlString .= "<strong>Fil:</strong> $file<hr>";				
		$htmlString .= "<strong>Rubrik:</strong> $subject<br><br>";				
		$htmlString .= "<strong>Beskrivning:</strong><br> $description<hr>";		
		$htmlString .= "<strong>Svar:</strong> $answer";		
									
		return $htmlString;				
	}


	function viewDetailModal2($ticketID,$groupID){
	
		$htmlString = "";
		
		$query = "	select
					`spform_tickets`.`id`,
					`spform_tickets`.`date`,
					`spform_users`.`namn`,
					`spform_users`.`efternamn`,
					`spform_users`.`adress`,
					`spform_users`.`postdress`,
					`spform_users`.`ort`,
					`spform_users`.`telefonnr`,
					`spform_users`.`mobilnr`,
					`spform_users`.`email`,
					`spform_tickets`.`subject`,
					`spform_tickets`.`description`,
					`spform_tickets`.`file`,
					`spform_tickets`.`answer`,
					`spform_tickets`.`log`,
					`spform_prio`.`prio`,
					`spform_status`.`status`,
					`spform_category`.`category` 
				from
					`spform_tickets` `spform_tickets` 
						inner join `spform_category` `spform_category` 
						on `spform_tickets`.`category_id` = `spform_category`.`id` 
							inner join `spform_users` `spform_users` 
							on `spform_tickets`.`user_id` = `spform_users`.`id` 
								inner join `spform_status` `spform_status` 
								on `spform_tickets`.`status_id` = `spform_status`.`id` 
									inner join `spform_prio` `spform_prio` 
									on `spform_tickets`.`prio_id` = `spform_prio`.`id`	
									WHERE `spform_tickets`.`id` = ? AND `spform_tickets`.`group_id` = ?";										
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('ii', $ticketID,$groupID);
		
		$stmt->execute();
	
		$stmt->bind_result($id,$date,$namn,$efternamn,$adress,$postdress,$ort,$telefonnr,$mobilnr,$email,$subject,$description,$file,$answer,$log,$prio,$status,$category);

		$stmt->fetch();
		
		$stmt->close();	
		
		
/* 		$htmlString .= 	"<div class='row'><div class='col-sm-4'>&Auml;rendeID: $id</div></div>"; */
		$htmlString .= "<h4>&Auml;rende #$id</h4><br>";
		$htmlString .= "<strong>Datum:</strong> $date<br>";		
		$htmlString .= "<strong>Prioritet:</strong> $prio<br>";		
		$htmlString .= "<strong>Status:</strong> $status<br>";
		$htmlString .= "<strong>Kategori:</strong> $category<br>";		
		$htmlString .= "<strong>Rubrik:</strong> $subject<br>";				
		$htmlString .= "<strong>Beskrivning:</strong><br> ".nl2br(utf8_encode($description))."<hr>";		
			
		$htmlString .= "<h4>S&auml;ndaren</h4><strong>Namn:</strong> $namn $efternamn<br>";	
		$htmlString .= "<strong>Adress:</strong><br>$adress<br>$postdress<br>$ort<br>";
		$htmlString .= "<strong>Telefon nr:</strong> $telefonnr<br>";	
		$htmlString .= "<strong>Mobil nr:</strong> $mobilnr<br>";				
		$htmlString .= "<strong>Epost:</strong> <a href='mailto:$email' target='_top'>$email</a><hr>";				
																	
/* 		$htmlString .= "<strong>Skickat till gruppen:</strong> $group<hr>"; */
		$htmlString .= "<strong>Fil:</strong> $file<hr>";				
		$htmlString .= "<strong>Svar:</strong> $answer<hr>";	
		$htmlString .= "<strong>Logg:</strong> $log";				
									
		return $htmlString;				
	}

	function viewDetailModal3($ticketID){
	
/* 		$query = "select date, submitter_id, answer, log, status from spform_answers WHERE ticket_id = ? ORDER BY id DESC"; */
		
		$htmlString = "";
		
		$query = "select
					`spform_answers`.`answer`,
					`spform_answers`.`log`,
					`spform_status`.`status`,
					`spform_answers`.`date`,
					`spform_users`.`username` 
				from
					`spform_answers` `spform_answers` 
						inner join `spform_status` `spform_status` 
						on `spform_answers`.`status` = `spform_status`.`id` 
							inner join `spform_users` `spform_users` 
							on `spform_answers`.`submitter_id` = `spform_users`.`id`	
						 WHERE `spform_answers`.`ticket_id` = ? ORDER BY `spform_answers`.`id` DESC";
		

		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $ticketID);

		$stmt->execute();
		
		$stmt->bind_result($answer,$log,$status,$date,$username);

		$htmlString .= "<h4>&Auml;rende #$ticketID</h4>";
		
		while ($stmt->fetch()) {
		
/* 			$username = $this->getUserName($submitter_id); */
		
			$htmlString .= "<strong>Datum:</strong> $date<br>";		
			$htmlString .= "<strong>Ansvarig:</strong> $username<br>";		
			$htmlString .= "<strong>Status:</strong> $status<br>";					
			$htmlString .= "<strong>Svar till kund:</strong><br> $answer<br>";
			$htmlString .= "<strong>Logg:</strong><br> $log<hr>";					
		}
		
		$stmt->close();		
				
		return $htmlString;				
	}



	function getGroups(){
	
		$query = "select * from spform_groups where id <> 1 and id <>2";				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$output = array( "aaData" => array());
		
		while($row = $rs->fetch_assoc()){
			
			$content = array();	
			
			$content[] = $row['id'];		
			$content[] = $row['group_name'];
			$content[] = "";						
									
			$output['aaData'][] = $content;
			
		}

		$rs->close();		
				
		return $output;
		
	}
	
	function getStatus(){
	
		$query = "select * from spform_status";				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$output = array( "aaData" => array());
		
		while($row = $rs->fetch_assoc()){
			
			$content = array();	
			
			$content[] = $row['id'];		
			$content[] = $row['status'];
			$content[] = "";						
									
			$output['aaData'][] = $content;
			
		}

		$rs->close();		
				
		return $output;
		
	}

	function getPrio(){
	
		$query = "select * from spform_prio";				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$output = array( "aaData" => array());
		
		while($row = $rs->fetch_assoc()){
			
			$content = array();	
			
			$content[] = $row['id'];		
			$content[] = $row['prio'];
			$content[] = "";						
									
			$output['aaData'][] = $content;
			
		}

		$rs->close();		
				
		return $output;
		
	}

	function getCategory(){
	
		$query = "select * from spform_category";				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$output = array( "aaData" => array());
		
		while($row = $rs->fetch_assoc()){
			
			$content = array();	
			
			$content[] = $row['id'];		
			$content[] = $row['category'];
			$content[] = "";						
									
			$output['aaData'][] = $content;
			
		}

		$rs->close();		
				
		return $output;
		
	}	
	
	function createGroupModal(){
	
		$htmlString = "";
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='groupname'>Gruppnamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='groupname' id='groupname' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 


		return $htmlString;		
				
	}	

	function createStatusModal(){
	
		$htmlString = "";
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='statusname'>Statusnamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='statusname' id='statusname' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 


		return $htmlString;		
				
	}	
	
	function createPrioModal(){
	
		$htmlString = "";
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='statusname'>Prioritetsnamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='prioname' id='prioname' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 


		return $htmlString;		
				
	}	
	
	function createCategoryModal(){
	
		$htmlString = "";
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='statusname'>Kategorinamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='categoryname' id='categoryname' >\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 


		return $htmlString;		
				
	}		
	
	function deleteGroup($id){	
	
		$query = "DELETE FROM spform_groups WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();
		
		$stmt->close();	
		
		return $result;				
		
	}

	function deleteStatus($id){	
	
		$query = "DELETE FROM spform_status WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();
		
		$stmt->close();	
		
		return $result;				
		
	}
	
	function deletePrio($id){	
	
		$query = "DELETE FROM spform_prio WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();
		
		$stmt->close();	
		
		return $result;				
		
	}

	function deleteCategory($id){	
	
		$query = "DELETE FROM spform_category WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();
		
		$stmt->close();	
		
		return $result;				
		
	}
	


	function getGroupIDs(){
	
		$query = "select * from spform_groups where id <> 1 and id <> 2";				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$content = array();	
			
		while($row = $rs->fetch_assoc()){
			
			$content[] = $row['id'];		
		}

		$rs->close();		
				
		return $content;
		
	}

	function getPrioIDs(){
	
		$query = "select * from spform_prio";				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$content = array();	
			
		while($row = $rs->fetch_assoc()){
			
			$content[] = $row['id'];		
		}

		$rs->close();		
				
		return $content;
		
	}


	function getStatusIDs(){
	
		$query = "select * from spform_status ORDER BY id ASC ";				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$content = array();	
			
		while($row = $rs->fetch_assoc()){
			
			$content[] = $row['id'];		
		}

		$rs->close();		
				
		return $content;
		
	}


	function getCategoryIDs(){
	
		$query = "select * from spform_category";				

		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$content = array();	
			
		while($row = $rs->fetch_assoc()){
			
			$content[] = $row['id'];		
		}

		$rs->close();		
				
		return $content;
		
	}


	function getGroupName($id){
	
		$query = "SELECT group_name FROM spform_groups WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();				
		
		$stmt->bind_result($groupName);
		
		$stmt->fetch();
		
		return $groupName;
		
	}

	function getGroupID($userID){
	
		$query = "SELECT groups FROM spform_users WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $userID);
	
		$result = $stmt->execute();				
		
		$stmt->bind_result($groupID);
		
		$stmt->fetch();
		
		return $groupID;
		
	}

	function getPrioName($id){
	
		$query = "SELECT prio FROM spform_prio WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();				
		
		$stmt->bind_result($prioName);
		
		$stmt->fetch();
		
		return $prioName;
		
	}
	
	function getCategoryName($id){
	
		$query = "SELECT category FROM spform_category WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();				
		
		$stmt->bind_result($categoryName);
		
		$stmt->fetch();
		
		return $categoryName;		
	}
	
	function getStatusName($id){
	
		$query = "SELECT status FROM spform_status WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result = $stmt->execute();				
		
		$stmt->bind_result($statusName);
		
		$stmt->fetch();
		
		return $statusName;
		
	}
	
	
	function addUser($data){	
		
		$username = $data[0];
		$email = $data[1];
		$password  = $data[2];
		$level = $data[3];
		$group = $data[4];

		$name = $data[5];
		$efternamn = $data[6];
		$adress = $data[7];	
		$postdress = $data[8];		
		$ort = $data[9];		
		$telefonnr = $data[10];
		$mobilnr = $data[11];
		$signatur = $data[12];	

		$today = date('Y-m-d');	
		
		$MD5_password = md5($password);
		
		$query = "INSERT INTO spform_users (username,email,password,level,groups,date_reg,namn,efternamn,adress,postdress,ort,telefonnr,mobilnr) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('sssiissssssss', $username,$email,$MD5_password,$level,$group,$today,$name,$efternamn,$adress,$postdress,$ort,$telefonnr,$mobilnr);
	
		$result = $stmt->execute();
		
		$stmt->close();	

		/*********************************************
		**
		**	Send a confirmation email to the customer
		**
		*********************************************/
		
		// Get email subject template		
		$emailSubject = $this->getEmailSubjectTemplate(1);
		
		// Get email body template				
		$emailBody = $this->getEmailBodyTemplate(1);
		
		// Translate level to text
		if($level == 2)
			$levelName = "HandlÃ¤ggare";
		else
			$levelName = "Kund";

		// Get sender name
		$senderName = $this->getSendersName();							
		// Get sender email	
		$sender = $this->getSendersEmail();		

		$url = preg_replace('/admin.php/', '', $this->curPageURL());
		
		$patterns = array();
		$patterns[0] = '/{username}/';
		$patterns[1] = '/{password}/';
		$patterns[2] = '/{level}/';
		$patterns[3] = '/{HTTP_link}/';
		
		$replacements = array();
		$replacements[0] = $username;
		$replacements[1] = $password;
		$replacements[2] = $levelName;
		$replacements[3] = $url;
		
		$message = preg_replace($patterns, $replacements, $emailBody);
		
		try {
			// minimal requirements to be set
			$mailer = new Mailer();
			$mailer->setFrom($senderName, $sender);
			$mailer->addRecipient($username,$email);
			$mailer->fillSubject(utf8_decode($emailSubject));
			$mailer->fillMessage(utf8_decode($message));
						
			// now we send it!
			$mailer->send();
		} catch (Exception $e) {
			echo $e->getMessage();
			exit(0);
		}
		
					
		
/* 		return $result; */
	}
	
	function addGroup($groupName){	
		
		$query = "INSERT INTO spform_groups (group_name) VALUES (?)";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $groupName);
	
		$result = $stmt->execute();
		
		$stmt->close();				
		
		return $result;
	}

	function addStatus($statusName){	
		
		$query = "INSERT INTO spform_status (status) VALUES (?)";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $statusName);
	
		$result = $stmt->execute();
		
		$stmt->close();				
		
		return $result;
	}

	function addPrio($prioName){	
		
		$query = "INSERT INTO spform_prio (prio) VALUES (?)";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $prioName);
	
		$result = $stmt->execute();
		
		$stmt->close();				
		
		return $result;
	}
	
	function addCategory($categoryName){	
		
		$query = "INSERT INTO spform_category (category) VALUES (?)";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $categoryName);
	
		$result = $stmt->execute();
		
		$stmt->close();				
		
		return $result;
	}
	
	
	
	function checkUserName($username){
		
		$query = "SELECT * FROM spform_users WHERE username = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s',$username);
	
		$stmt->execute();
		
		$stmt->store_result();
	
		$result = $stmt->num_rows;
		
		return $result;
	}
	
	function checkEmail($email){
		
		$query = "SELECT * FROM spform_users WHERE email = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s',$email);
	
		$stmt->execute();
		
		$stmt->store_result();
	
		$result = $stmt->num_rows;
		
		return $result;
	}
	

	function editUserModal($userID){
	
		$htmlString = "";
		
		// Check user level
		$level = $this->getLevelByUserid($userID);
		
		$group_list = array();		
		$group_list = $this->getGroupIDs();

		$query = "SELECT username,email,level,groups,namn,efternamn,adress,postdress,ort,telefonnr,mobilnr FROM spform_users WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $userID);
		
		$stmt->execute();
	
		$stmt->bind_result($username,$email,$level,$group,$namn,$efternamn,$adress,$postdress,$ort,$telefonnr,$mobilnr);
	
		$stmt->fetch();
		
		$stmt->close();						

		$selected0 = ($level == 1) ? "selected" : "";		
		$selected1 = ($level == 2) ? "selected" : "";
		$selected2 = ($level == 3) ? "selected" : "";
		$selected3 = ($level == 4) ? "selected" : "";
		
		$htmlString = "";
		
		$htmlString .= "<input type='hidden' name='id' value=$userID>\n"; 		
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Av&auml;ndarnamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='username' id='username' value=$username disabled>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>E-post *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='email' id='email' value=$email>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 


		if($userID != 1){

			$htmlString .= "<div class='row'> \n"; 
			$htmlString .= "	<div class='col-lg-4'> \n"; 
			$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
			$htmlString .= "          <label for='level'>Beh&ouml;righet *</label> \n"; 
			$htmlString .= "			<select name='level' class=\"form-control\" id=\"comboA\" onchange=\"getUserLevel(this)\">\n"; 
/* 			$htmlString .= "  				<option value=1 $selected0>Admin</option>\n";  */
			$htmlString .= "  				<option value=2 $selected1>Handl&auml;ggare</option>\n"; 
	/* 		$htmlString .= "  				<option value=3 $selected2>Koordinator</option>\n";  */
			$htmlString .= "  				<option value=4 $selected3>Kund</option>\n"; 
			$htmlString .= "			</select>\n";		
			$htmlString .= "        </div> \n"; 
			$htmlString .= "	</div> \n"; 
			$htmlString .= "</div>\n"; 
			

			if($level == 2)
				$htmlString .= "<div id='groupSelection'> \n"; 		
			else
				$htmlString .= "<div id='groupSelection' style='display: none;'> \n"; 
			
			$htmlString .= "<div class='row'> \n"; 
			$htmlString .= "	<div class='col-lg-4'> \n"; 
			$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
			$htmlString .= "          <label for='level'>Grupp *</label> \n"; 
			$htmlString .= "			<select name='group' class=\"form-control\">\n"; 
			
			foreach($group_list as $value){
			
				$selected4 = ($group == $value) ? "selected" : "";
			
				$htmlString .= "<option value=$value $selected4>".$this->getGroupName($value)."</option>\n"; 
			
			}
					
			$htmlString .= "			</select>\n";		
			$htmlString .= "        </div> \n"; 
			$htmlString .= "	</div> \n"; 
			$htmlString .= "</div>\n"; 
			
			$htmlString .= "</div>\n"; 
		}	
		
		$htmlString .= "<hr><div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Namn</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='name' id='name' value=$namn>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Efternamn</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		
		$htmlString .= "            <input class='form-control' type='text' name='efternamn' id='efternamn' value=$efternamn>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" >Adress</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='adress' id='adress' value=$adress>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Postdress</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='postdress' id='postdress' value=$postdress>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Ort</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='ort' id='ort' value=$ort>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Telefonnr</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='telefonnr' id='telefonnr' value=$telefonnr>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Mobilnr</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='mobilnr' id='mobilnr' value=$mobilnr>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 
 			
		return $htmlString;
	
	}	

	function editUserModal2($userID){
	
		$htmlString = "";
		
		$group_list = array();		
		$group_list = $this->getGroupIDs();

		$query = "SELECT username,email,namn,efternamn,adress,postdress,ort,telefonnr,mobilnr FROM spform_users WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $userID);
		
		$stmt->execute();
	
		$stmt->bind_result($username,$email,$namn,$efternamn,$adress,$postdress,$ort,$telefonnr,$mobilnr);
	
		$stmt->fetch();
		
		$stmt->close();						

		$htmlString = "";
		
		$htmlString .= "<input type='hidden' name='id' value=$userID>\n"; 		
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Av&auml;ndarnamn</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='username' id='username' value=$username disabled>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>E-post</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='email' id='email' value=$email disabled>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Namn</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='name' id='name' value=$namn>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Efternamn</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		
		$htmlString .= "            <input class='form-control' type='text' name='efternamn' id='efternamn' value=$efternamn>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" >Adress</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='adress' id='adress' value=$adress>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Postdress</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='postdress' id='postdress' value=$postdress>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-6'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Ort</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='ort' id='ort' value=$ort>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Telefonnr</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='telefonnr' id='telefonnr' value=$telefonnr>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Mobilnr</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='mobilnr' id='mobilnr' value=$mobilnr>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 
 			
		return $htmlString;
	
	}	
	
	function updateUser($data){	
		
		$id = $data[0]; 
		$username = $data[1];
		$email = $data[2];
		$level = $data[3];
		$group = $data[4];

		$namn = $data[5];
		$efternamn = $data[6];
		$adress = $data[7];	
		$postdress = $data[8];		
		$ort = $data[9];		
		$telefonnr = $data[10];
		$mobilnr = $data[11];		

		$query = "UPDATE spform_users SET email = ?,level = ?,groups = ?,namn = ?,efternamn = ?,adress = ?,postdress = ?,ort = ?,telefonnr = ?,mobilnr = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('siisssssssi',$email,$level,$group,$namn,$efternamn,$adress,$postdress,$ort,$telefonnr,$mobilnr,$id);
		
		$result =  $stmt->execute();
		
		$stmt->close();				
	
		return $result;

	}

	function updateUser2($data){	
		
		$id = $data[0]; 
		$namn = $data[1];
		$efternamn = $data[2];
		$adress = $data[3];	
		$postdress = $data[4];		
		$ort = $data[5];		
		$telefonnr = $data[6];
		$mobilnr = $data[7];		

		$query = "UPDATE spform_users SET namn = ?,efternamn = ?,adress = ?,postdress = ?,ort = ?,telefonnr = ?,mobilnr = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('sssssssi',$namn,$efternamn,$adress,$postdress,$ort,$telefonnr,$mobilnr,$id);
		
		$result =  $stmt->execute();
		
		$stmt->close();				
	
		return $result;

	}


	function updateGroup($groupID,$groupName){	
		
		$query = "UPDATE spform_groups SET group_name = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('si',$groupName,$groupID);
		
		$result =  $stmt->execute();
		
		$stmt->close();				
	
		return $result;

	}
	
	function updateStatus($statusID,$statusName){	
		
		$query = "UPDATE spform_status SET status = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('si',$statusName,$statusID);
		
		$result =  $stmt->execute();
		
		$stmt->close();				
	
		return $result;

	}

	function updatePrio($prioID,$prioName){	
		
		$query = "UPDATE spform_prio SET prio = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('si',$prioName,$prioID);
		
		$result =  $stmt->execute();
		
		$stmt->close();				
	
		return $result;

	}
	
	
	function updateCategory($categoryID,$categoryName){	
		
		$query = "UPDATE spform_category SET category = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('si',$categoryName,$categoryID);
		
		$result =  $stmt->execute();
		
		$stmt->close();				
	
		return $result;

	}

	function updateTicket($data){	
		
		$id = $data[0]; 
		$prio = $data[1];
		$status = $data[2];
		$answer = $data[3];
		$log = $data[4];
		$submitterID = $data[5];
				
		$subject = $data[6];
		$description = $data[7];				

		$query = "UPDATE spform_tickets SET prio_id = ?,status_id = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('iii',$prio,$status,$id);
		
		$result =  $stmt->execute();
		
		$stmt->close();				

				
		// Save the answer in the spform_answers table
		
		$query = "INSERT INTO spform_answers (ticket_id, submitter_id, answer,log,status) VALUES (?,?,?,?,?)";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('iissi',$id,$submitterID,$answer,$log,$status);
	
		$result = $stmt->execute();
		
		$stmt->close();	
	
/* 		return $result; */

		/*********************************************
		**
		**	Send the answer to the customer
		**
		*********************************************/
		
		// Get email subject template		
		$emailSubject = $this->getEmailSubjectTemplate(3);
		
		// Get email body template				
		$emailBody = $this->getEmailBodyTemplate(3);

		// Get sender name
		$senderName = $this->getSendersName();							
		// Get sender email	
		$sender = $this->getSendersEmail();		
		
		// Get customer email + username
		$userData = $this->getUserDataFromTicket($id);  
		
		$customerUsername = $userData[0];
		$customerEmail = $userData[1];		
		
		$patterns = array();
		$patterns[0] = '/{id}/';		
		$patterns[1] = '/{prio}/';		
		$patterns[2] = '/{status}/';		
		$patterns[3] = '/{subject}/';		
		$patterns[4] = '/{description}/';
		$patterns[5] = '/{answer}/';		
		
		$replacements = array();
		$replacements[0] = $id;
		$replacements[1] = $this->getPrioName($prio);
		$replacements[2] = $this->getStatusName($status);
		$replacements[3] = $subject;
		$replacements[4] = $description;
		$replacements[5] = $answer;		
	
		$emailSubject = preg_replace($patterns, $replacements, $emailSubject);							
		$message = preg_replace($patterns, $replacements, $emailBody);
		
		try {
			// minimal requirements to be set
			$mailer = new Mailer();
			$mailer->setFrom($senderName, $sender);
			$mailer->addRecipient($customerUsername,$customerEmail);	//<--- customer
			$mailer->fillSubject(utf8_decode($emailSubject));
			$mailer->fillMessage(utf8_decode($message));
						
			// now we send it!
			$mailer->send();
		} catch (Exception $e) {
			echo $e->getMessage();
			exit(0);
		}
		
	}
	
	function updateEmailSettings($data){		

		$id = $data[0]; 
		$sender = $data[1];
		$emailSubject = $data[2];
		$emailBody = $data[3];
		$senderName = $data[4];		
		
/* 		$emailFooter = $data[4]; */

/* 		$query = "UPDATE spform_email SET sender = ?,subject = ?,body = ?,footer = ? WHERE id = ?"; */
		$query = "UPDATE spform_email SET sender = ?,subject = ?,body = ?, sender_name = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
/* 		$stmt->bind_param('ssssi',$sender,$emailSubject,$emailBody,$emailFooter,$id); */
		$stmt->bind_param('ssssi',$sender,$emailSubject,$emailBody,$senderName,$id);
		
		$result =  $stmt->execute();
		
		$stmt->close();				
	
		return $result;

	}
	
	function changePasswordModal($userID){
	
		$htmlString = "";
		
		$htmlString .= "<input type='hidden' name='id' value=$userID>\n"; 		
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Nytt l&ouml;senord</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='password' name='newPassword' id='newPassword'>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>Bekr&auml;fta l&ouml;senord</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='password' name='confirmNewPassword' id='confirmNewPassword'>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		return $htmlString;
	
	}	

	function updatePassword($userID,$password){	
	
		$query = "UPDATE spform_users SET password = ? WHERE id = ?";
		
		$MD5_password = md5($password);
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('si', $MD5_password,$userID);
	
		$result = $stmt->execute();
		
		$stmt->close();		
		
		return $result;		
	}
	

	function editGroupModal($groupID){
	
		$htmlString = "";
		
		$query = "SELECT group_name FROM spform_groups WHERE id = ? ";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $groupID);
		
		$stmt->execute();
	
		$stmt->bind_result($groupname);
	
		$stmt->fetch();
		
		$stmt->close();						

		$htmlString .= "<input type='hidden' name='id' value=$groupID>\n"; 		
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Gruppnamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='groupname' id='groupname' value=$groupname>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		return $htmlString;
	
	}	

	function editStatusModal($statusID){
	
		$htmlString = "";
		
		$query = "SELECT status FROM spform_status WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $statusID);
		
		$stmt->execute();
	
		$stmt->bind_result($statusname);
	
		$stmt->fetch();
		
		$stmt->close();						

		$htmlString .= "<input type='hidden' name='id' value=$statusID>\n"; 		
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Gruppnamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='statusname' id='statusname' value=$statusname>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		return $htmlString;
	
	}	
	
	function editPrioModal($prioID){
	
		$htmlString = "";
		
		$query = "SELECT prio FROM spform_prio WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $prioID);
		
		$stmt->execute();
	
		$stmt->bind_result($prioname);
	
		$stmt->fetch();
		
		$stmt->close();						

		$htmlString .= "<input type='hidden' name='id' value=$prioID>\n"; 		
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Prioritetsnamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='prioname' id='prioname' value=$prioname>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		return $htmlString;
	
	}	
	
	function editCategoryModal($categoryID){
	
		$htmlString = "";
		
		$query = "SELECT category FROM spform_category WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $categoryID);
		
		$stmt->execute();
	
		$stmt->bind_result($categoryname);
	
		$stmt->fetch();
		
		$stmt->close();						

		$htmlString .= "<input type='hidden' name='id' value=$categoryID>\n"; 		
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='username'>Kategorinamn *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='categoryname' id='categoryname' value=$categoryname>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		return $htmlString;
	
	}	

	function changeEmailModal($userID){
	
		$htmlString = "";
		
		$query = "SELECT email FROM spform_users WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $userID);
		
		$stmt->execute();
	
		$stmt->bind_result($email);
	
		$stmt->fetch();
		
		$stmt->close();						

		$htmlString .= "<input type='hidden' name='id' value=$categoryID>\n"; 		
		
		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\" for='taskSubject'>E-post *</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "            <input class='form-control' type='text' name='email' id='email' value=$email>\n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 
		
		return $htmlString;
	
	}		
	
	function editTicketModal($ticketID){
		
		$htmlString = "";
		
		$query = "select
				`spform_tickets`.`id`,
				`spform_tickets`.`date`,
				`spform_tickets`.`subject`,
				`spform_tickets`.`log`,
				`spform_tickets`.`answer`,
				`spform_tickets`.`description`,
				`spform_tickets`.`prio_id`,
				`spform_tickets`.`status_id` 				
			from
				`spform_tickets` `spform_tickets` 
					inner join `spform_status` `spform_status` 
					on `spform_tickets`.`status_id` = `spform_status`.`id` 
						inner join `spform_prio` `spform_prio` 
						on `spform_tickets`.`prio_id` = `spform_prio`.`id` 
						WHERE `spform_tickets`.id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $ticketID);
		
		$stmt->execute();
	
		$stmt->bind_result($id,$date,$subject,$log,$answer,$description,$prio_id,$status_id);
	
		$stmt->fetch();
		
		$stmt->close();						

		$htmlString .= "<input type='hidden' name='id' value=$ticketID>\n"; 				
		
		$htmlString .= "<strong>&Auml;rende #:</strong> $ticketID<br>\n"; 
		$htmlString .= "<strong>Datum:</strong> $date<hr>\n"; 

		$prio_list = array();		
		$prio_list = $this->getPrioIDs();

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label for='level'>Prioritet</label> \n"; 
		$htmlString .= "			<select name='prio' class=\"form-control\">\n"; 
		
		foreach($prio_list as $value){
		
			$selected = ($prio_id == $value) ? "selected" : "";
							
			$htmlString .= "<option value=$value $selected>".$this->getPrioName($value)."</option>\n"; 
		
		}
				
		$htmlString .= "			</select>\n";		
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div>\n"; 


		$status_list = array();		
		$status_list = $this->getStatusIDs();

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-4'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label for='level'>Status</label> \n"; 
		$htmlString .= "			<select name='status' class=\"form-control\">\n"; 
		
		foreach($status_list as $value){
		
			$selected = ($status_id == $value) ? "selected" : "";
							
			$htmlString .= "<option value=$value $selected>".$this->getStatusName($value)."</option>\n"; 
		
		}
				
		$htmlString .= "			</select>\n";		
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div><hr>\n"; 

		$htmlString .= "<strong>Rubrik:</strong> $subject<br>\n"; 		
		$htmlString .= "<strong>Beskrivning:</strong><br>".nl2br(utf8_encode($description))."<hr>";


		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Svar till kunden:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <textarea class='form-control' rows='5' name='answer' id='answer'>$answer</textarea> \n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div><hr> \n"; 

		$htmlString .= "<div class='row'> \n"; 
		$htmlString .= "	<div class='col-lg-8'> \n"; 
		$htmlString .= "        <div class='control-group' style='margin-bottom: 10px;'> \n"; 
		$htmlString .= "          <label class=\"control-label\">Egna anteckningar:</label> \n"; 
		$htmlString .= "          <div class='controls'> \n"; 
		$htmlString .= "			  <textarea class='form-control' rows='5' name='log' id='log'>$log</textarea> \n"; 
		$htmlString .= "          </div> \n"; 
		$htmlString .= "        </div> \n"; 
		$htmlString .= "	</div> \n"; 
		$htmlString .= "</div> \n"; 

		$htmlString .= "<input type='hidden' name='subject' value=$subject>\n"; 				
		$htmlString .= "<input type='hidden' name='description' value=$description>\n"; 				
		
		return $htmlString;		
		
	}
	
	function getLevel($email){
	
		// level 1=Admin 2=Handläggare 3=Koordinator 4=Kund
		
		$query = "SELECT level FROM spform_users WHERE email = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $email);
	
		$result = $stmt->execute();				
		
		$stmt->bind_result($level);
		
		$stmt->fetch();
		
		return $level;
	}

	function getLevelByUserid($userID){
	
		// level 1=Admin 2=Handläggare 3=Koordinator 4=Kund
		
		$query = "SELECT level FROM spform_users WHERE email = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $userID);
	
		$result = $stmt->execute();				
		
		$stmt->bind_result($level);
		
		$stmt->fetch();
		
		return $level;
	}


	function getUserId($email){
		
		$query = "SELECT id FROM spform_users WHERE email = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('s', $email);
	
		$result = $stmt->execute();				
		
		$stmt->bind_result($id);
		
		$stmt->fetch();
		
		return $id;
	}
	

	function addTicket($data){	
	
		$date  = $data[0];
		$userid  = $data[1];		
		$username  = $data[2]; 
		$email  = $data[3]; 
		$status  = $data[4]; 		
		$prio  = $data[5]; 	
		$category  = $data[6]; 		
		$group  = $data[7]; 		
		$subject  = $data[8]; 
		$description  = $data[9]; 
		$filename  = $data[10]; 
								
		$query = "INSERT INTO spform_tickets (date,user_id,username,email,status_id,prio_id,category_id,group_id,subject,description,file) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('sissiiiisss', $date,$userid,$username,$email,$status,$prio,$category,$group,$subject,$description,$filename);
	
		$result = $stmt->execute();
		
		$ticketID = $stmt->insert_id;
		
		$stmt->close();				
		
		
		/*********************************************
		**
		**	Send a confirmation email to the customer
		**
		*********************************************/
		
		// Get email subject template		
		$emailSubject = $this->getEmailSubjectTemplate(2);
		
		// Get email body template				
		$emailBody = $this->getEmailBodyTemplate(2);

		// Get sender name
		$senderName = $this->getSendersName();							
		// Get sender email	
		$sender = $this->getSendersEmail();		
		
		$patterns = array();
		$patterns[0] = '/{id}/';		
		$patterns[1] = '/{prio}/';		
		$patterns[2] = '/{status}/';		
		$patterns[3] = '/{subject}/';		
		$patterns[4] = '/{description}/';
		
		$replacements = array();
		$replacements[0] = $ticketID;
		$replacements[1] = $this->getPrioName($prio);
		$replacements[2] = $this->getStatusName($status);
		$replacements[3] = $subject;
		$replacements[4] = $description;
	
		$emailSubject = preg_replace($patterns, $replacements, $emailSubject);							
		$message = preg_replace($patterns, $replacements, $emailBody);
		
		try {
			// minimal requirements to be set
			$mailer = new Mailer();
			$mailer->setFrom($senderName, $sender);
			$mailer->addRecipient($username,$email);
			$mailer->fillSubject(utf8_decode($emailSubject));
			$mailer->fillMessage(utf8_decode($message));
						
			// now we send it!
			$mailer->send();
		} catch (Exception $e) {
			echo $e->getMessage();
			exit(0);
		}


		/*********************************************
		**
		**	Send a email to the service group
		**
		*********************************************/
		
		// Get email subject template		
		$emailSubject = $this->getEmailSubjectTemplate(4);
		
		// Get email body template				
		$emailBody = $this->getEmailBodyTemplate(4);

		// Get sender name
		$senderName = $this->getSendersName();							
		// Get sender email	
		$sender = $this->getSendersEmail();		
		
		// Get emails to all members in service group
		
		$serviceGroupEmails = $this->getEmailServiceGroup($group);
		
		$patterns = array();
		$patterns[0] = '/{id}/';		
		$patterns[1] = '/{prio}/';		
		$patterns[2] = '/{status}/';		
		$patterns[3] = '/{subject}/';		
		$patterns[4] = '/{description}/';
		
		$replacements = array();
		$replacements[0] = $ticketID;
		$replacements[1] = $this->getPrioName($prio);
		$replacements[2] = $this->getStatusName($status);
		$replacements[3] = $subject;
		$replacements[4] = $description;
	
		$emailSubject = preg_replace($patterns, $replacements, $emailSubject);							
		$message = preg_replace($patterns, $replacements, $emailBody);
		
		try {
			// minimal requirements to be set
			$mailer = new Mailer();
			$mailer->setFrom($senderName, $sender);

			
			$query = "select username,email from spform_users where groups = $group";

			$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
			
			$rs->data_seek(0);
			
			$content = array();	
				
			while($row = $rs->fetch_assoc()){
				
				$username = $row['username'];		
				$email = $row['email'];						
				
				$mailer->addRecipient($username,$email); // <---- Send email to the group				
			}
	
			$rs->close();		

			$mailer->fillSubject(utf8_decode($emailSubject));
			$mailer->fillMessage(utf8_decode($message));
						
			// now we send it!
			$mailer->send();
		} catch (Exception $e) {
			echo $e->getMessage();
			exit(0);
		}
		
	}

	// Create a ticket from email...
	
	function addTicket2($data){	
	
		$date = $data[0];
		$subject = $data[1];
		$email = $data[2];
		$content = $data[3];

		$defaultGroupId =	$data[4];
		$defaultPrioId = $data[5];
		$defaultStatusId = $data[6];
		$defaultCategoriId = $data[7]; 																	

		$userid = $data[8]; 		
			
		$query = "INSERT INTO spform_tickets (date,subject,email,description,group_id,prio_id,status_id,category_id,user_id) VALUES (?,?,?,?,?,?,?,?,?)";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('ssssiiiii', $date,$subject,$email,$content,$defaultGroupId,$defaultPrioId,$defaultStatusId,$defaultCategoriId,$userid);
	
		$result = $stmt->execute();
		
		$stmt->close();				
		
/* 		return $result; */

		return 	$this->mysqli->insert_id;
	}
	
	

	function getTickets($userID){
		
		$query = "select
			`spform_tickets`.`id`,
			`spform_tickets`.`date`,
			`spform_prio`.`prio`,							
			`spform_status`.`status`,
			`spform_tickets`.`subject`,
			`spform_tickets`.`file` 			
		from
			`spform_status` `spform_status` 
				inner join `spform_tickets` `spform_tickets` 
				on `spform_status`.`id` = `spform_tickets`.`status_id`
					inner join `spform_prio` `spform_prio` 
					on `spform_tickets`.`prio_id` = `spform_prio`.`id`						
				WHERE user_id = ? ORDER BY `spform_tickets`.`id` DESC";
		

		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $userID);

		$stmt->execute();
		
		$stmt->bind_result($id,$date,$subject,$status,$prio,$file);

		$output = array( "aaData" => array());

		while ($stmt->fetch()) {
		
			$content = array();

			$fileLink = "<a href='upload/$file' target='_blank'>$file</a>";

			$content[] = $id;		
			$content[] = $date;														
			$content[] = $prio;			
			$content[] = $status;
			$content[] = utf8_encode($subject);
			$content[] = $fileLink;			
			$content[] = "";

			$output['aaData'][] = $content;
		
		}
		
		$stmt->close();		
				
		return $output;
		
	}
	
	
	function getTickets2($groupID){
		
		
		$query = "select
				`spform_tickets`.`id`,
				`spform_tickets`.`date`,
				`spform_prio`.`prio`,				
				`spform_status`.`status`,
				`spform_tickets`.`subject`,
				`spform_tickets`.`file`,
				`spform_users`.`username` 
			from
				`spform_tickets` `spform_tickets` 
					inner join `spform_users` `spform_users` 
					on `spform_tickets`.`user_id` = `spform_users`.`id` 
						inner join `spform_status` `spform_status` 
						on `spform_tickets`.`status_id` = `spform_status`.`id`
							inner join `spform_prio` `spform_prio` 
							on `spform_tickets`.`prio_id` = `spform_prio`.`id`
						WHERE `spform_tickets`.`group_id` = ? ORDER BY `spform_tickets`.`id` DESC";			
					

		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $groupID);

		$stmt->execute();
		
		$stmt->bind_result($id,$date,$subject,$prio,$status,$file,$username);

		$output = array( "aaData" => array());

		while ($stmt->fetch()) {
		
			$content = array();

			$fileLink = "<a href='upload/$file' target='_blank'>$file</a>";
			
			$content[] = $id;		
			$content[] = $date;		
			$content[] = $username;			
			$content[] = $status;									
			$content[] = $prio;
			$content[] = $subject;			
			$content[] = $fileLink;			
			$content[] = "";

			$output['aaData'][] = $content;
		
		}
		
		$stmt->close();		
				
		return $output;
		
	}


	function getEmailSubjectTemplate($id){
		
		$query = "SELECT subject FROM spform_email WHERE id = ?";
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
		
		$stmt->execute();
		
		$stmt->bind_result($subject);
	
		$stmt->fetch();

		return $subject;		
	}

	function getEmailBodyTemplate($id){
		
		$query = "SELECT body FROM spform_email WHERE id = ?";
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
		
		$stmt->execute();
		
		$stmt->bind_result($body);
	
		$stmt->fetch();

		return $body;		
	}

	function getSendersEmail(){
		
		$query = "SELECT sender FROM spform_email limit 1";
		
		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$row = $rs->fetch_assoc();
			
		$sender = $row['sender'];		

		$rs->close();		
		
		return $sender;				
		
	}

	function getSendersName(){
		
		$query = "SELECT sender_name FROM spform_email limit 1";
		
		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));
		
		$rs->data_seek(0);
		
		$row = $rs->fetch_assoc();
			
		$senderName = $row['sender_name'];		

		$rs->close();		
		
		return $senderName;				
		
	}


	function getSupportformVersion($id){
		
		$query = "SELECT value FROM spform_config WHERE variable_id = ?";
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
		
		$stmt->execute();
		
		$stmt->bind_result($value);
	
		$stmt->fetch();

		return $value;				
	}

	function curPageURL() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		return $pageURL;
	}


	function getEmailServiceGroup($groupID){
	
		$groupEmails = "";
		
		$query = "select email from spform_users where groups = ?";
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $groupID);
		
		$stmt->execute();
		
		$stmt->bind_result($email);	

		while($stmt->fetch()){
		
			$groupEmails .= $email.",";

		}											

		$stmt->free_result();		
		
		// Remove the last ','
													
		$groupEmails = rtrim($groupEmails,',');

		return $groupEmails;
		
	}

	function getServiceResponsble($id){
		
		$query = "SELECT submitter_id FROM spform_answers WHERE ticket_id = ? ORDER BY id DESC limit 1"; //<-- get name
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
		
		$stmt->execute();
		
		$stmt->bind_result($body);
	
		$stmt->fetch();

		return $body;		
		
	}


	function getUserName($userID){
				
		$query = "SELECT username FROM spform_users WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $userID);
		
		$stmt->execute();
	
		$stmt->bind_result($username);
	
		$stmt->fetch();
		
		return $username;
		
	}

	function getUserDataFromTicket($ticketID){
		
		$data = array();
				
		$query = "SELECT username,email FROM spform_tickets WHERE id = ?";
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $ticketID);
		
		$stmt->execute();
		
		$stmt->store_result();

		if($stmt->num_rows){
			
			$stmt->bind_result($username,$email);
		
			$stmt->fetch();
			
			$data[] = $username;
			$data[] = $email;
		}
		
		return $data;
		
	}


	function object_to_array($the_object)
	{
	   $the_array=array();
	   if(!is_scalar($the_object))
	   {
	       foreach($the_object as $id => $object)
	       {
	           if(is_scalar($object))
	           {
	               $the_array[$id]=$object;
	           }
	           else
	           {
	               $the_array[$id]=$this->object_to_array($object);
	           }
	       }
	       return $the_array;
	   }
	   else
	   {
	       return $the_object;
	   }
	}



/* ---------------------- / Supportorm V2 ------------------------ */








	function checkEmailIDInTaskList($userID,$mailID){
				
		$query = "SELECT * FROM tickets WHERE userid = ? AND mailid = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('ii', $userID,$mailID);

		$stmt->execute();
			
		$stmt->store_result();
	
		$result = $stmt->num_rows;
		
		$stmt->close();				
		
		return $result;		
	}

	function checkAccount($email, $password){
	
		$query = "SELECT * FROM spform_users WHERE email = ? AND password = ?";				
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('ss', $email,$password);
		
		$stmt->execute();
		
		$stmt->store_result();
	
		$result = $stmt->num_rows;
		
		$stmt->close();				
		
		return $result;
	}
	

	function getMailboxSettings(){
				
		$data = array();
				
		$query = "SELECT server_type,path_prefix,mailbox,port,SSLport,hostname,mailboxuser,mailboxpassword,default_group,default_prio,default_status,default_categori FROM spform_mailboxes";
		
		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));		
		$rs->data_seek(0);
		
		while($row = $rs->fetch_assoc()){
			
			$data[] = $row['server_type'];	
			$data[] = $row['path_prefix'];			
			$data[] = $row['mailbox'];			
			$data[] = $row['port'];	
			$data[] = $row['SSLport'];				
			$data[] = $row['hostname'];						
			$data[] = $row['mailboxuser'];	
			$data[] = $row['mailboxpassword'];
			
			$data[] = $row['default_group'];
			$data[] = $row['default_prio'];
			$data[] = $row['default_status'];
			$data[] = $row['default_categori'];																
		}

		$rs->close();		
		
		return $data;
		
	}


	function checkMailSettingsStatus(){				
	
		$data = array();
				
		$query = "SELECT status FROM spform_mailboxes";
		
		$rs=$this->mysqli->query($query) or die(mysqli_error($this->$mysqli));		
		$rs->data_seek(0);
		
		while($row = $rs->fetch_assoc()){
			
			$data[] = $row['status'];	
		}

		$rs->close();		
		
		return $data[0];
	
	}

	function setMailSettingsStatus($status,$id){
				
		$query = "UPDATE spform_mailboxes SET status = ? WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('ii', $status,$id);
	
		$result = $stmt->execute();
		
		$stmt->close();				
	
		return $result;
		
	}


	function getUserInfo($userID){
				
		$query = "SELECT email FROM users WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
		
		$stmt->execute();
	
		$stmt->bind_result($email);
	
		$stmt->fetch();
		
		return $email;
		
	}
	
	function updateMailboxSettings($data){	
		
		$userID = $data[0];
		$serverType = $data[1];
		$pathPrefix  = $data[2];
		$port = $data[3];
		$SSL = $data[4];
		$hostname = $data[5];
		$mailboxUser = $data[6];
		$mailboxPassword = $data[7];
		$mailbox  = $data[8];
		
		$groupId = $data[9];
		$categoryId = $data[10];
		$statusId = $data[11];
		$prioId =	$data[12];		
				
		if(!$SSL)
			$SSL = 0;
		else
			$SSL = 1;

		$id = 1;
		
		$query = "UPDATE spform_mailboxes SET server_type = ?, path_prefix = ?, port = ?, SSLport = ?, hostname = ?, mailboxuser = ?, 
					mailboxpassword = ?, mailbox = ?, default_group = ?, default_categori = ?, default_status = ?, default_prio = ?	WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('isiissssiiiii',$serverType,$pathPrefix,$port,$SSL,$hostname,$mailboxUser,$mailboxPassword,$mailbox,$groupId,$categoryId,$statusId,$prioId,$id);
	
		$result = $stmt->execute();
		
		$stmt->close();		
		
		return $result;		
	}
	
	function listTickets($userID){
		
		$query = "SELECT date,status,priority,subject,sender,comment FROM tickets WHERE userid = ? ORDER BY id DESC ";
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $userID);
		
		$stmt->execute();
		
		$stmt->bind_result($date,$status,$priority,$subject,$sender,$comment);
		
//		echo "<table class='table table-bordered responsive'>"; 

		echo "<table cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' id='example'>";
		echo "<thead>"; 
		echo "<tr>"; 
		echo "<th>Date</th>"; 
		echo "<th>Status</th>"; 
		echo "<th>Priority</th>"; 
		echo "<th>Subject</th>"; 
		echo "<th>Sender</th>"; 
		echo "<th>Comment</th>"; 						
		echo "<th>Operations</th>"; 
		echo "</tr>"; 
		echo "</thead>"; 
		echo "<tbody>"; 
		
		while ($stmt->fetch()) {
			
			$id = $row['id'];
			
			echo "<tr>";
		    echo "<td>".$date."</td>";
		    echo "<td>".$status."</td>";											    
		    echo "<td>".$priority."</td>";
		    echo "<td>".$subject."</td>";
		    echo "<td>".$sender."</td>";		    
		    echo "<td>".$comment."</td>";
		    		    		    		    		    	
			echo "<td>";												
			echo "<a href=\"#editCustomerModal\" data-toggle=\"modal\" rel=\"tooltip\" data-original-title=\"Edit customer\" data-placement=\"bottom\" class=\"btn edit\" id=\"customerName\">";	
			echo "<i class=\"icon-edit\"></i></a>"; 
			
			
			echo "<a role=\"button\" class=\"btn btn-danger remove1\"  href=\"list_customers.php?delete=$id\"><i class=\"icon-remove\"></i></a>";
			echo "</td></tr>";												
		}												
	
		echo "</tbody></table>";
		
	}

	function closeTicket($id){
				
		$query = "UPDATE tickets SET status = 'Done' WHERE id = ?";
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param('i', $id);
	
		$result =  $stmt->execute();
		
		$stmt->close();				
	
		return $result;
	}
	
	
}