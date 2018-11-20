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


include('include/DBHandler.php');
	
$DBhandler = new DBHandler();


if(isset($_POST['testConnection'])){

	$data = array();
	
	$data = $DBhandler->getMailboxSettings();

	$mailbox = $data[2];
	$mailboxUser = $data[6];
	$mailboxPassword = $data[7];

	// Save to session
	$_SESSION['mailboxPassword'] = 	$mailboxPassword;
	
	$inbox = @imap_open($mailbox,$mailboxUser,$mailboxPassword);

	if(!$inbox)
		echo "<div class='alert alert-danger' style='position: relative; top: 10px;'>".imap_last_error()."</div>";		
	else{
	
		$DBhandler->setMailSettingsStatus($userID,1);
		
		echo "<div class='alert alert-success' style='position: relative; top: 10px;'>F&ouml;rbindelsen fungerar!</div>";
	}

}

if(isset($_POST['getUsers'])){

	$result = array();
	
	$result = $DBhandler->getUsers();
		
	if($result)
		echo json_encode($result);
	else{
		
		echo '{
		    "sEcho": 1,
		    "iTotalRecords": "0",
		    "iTotalDisplayRecords": "0",
		    "aaData": []
		}';							
	}
}


if(isset($_POST['getGroups'])){

	$result = array();
	
	$result = $DBhandler->getGroups();
		
	if($result)
		echo json_encode($result);
	else{
		
		echo '{
		    "sEcho": 1,
		    "iTotalRecords": "0",
		    "iTotalDisplayRecords": "0",
		    "aaData": []
		}';							
	}
}

if(isset($_POST['getStatus'])){

	$result = array();
	
	$result = $DBhandler->getStatus();
		
	if($result)
		echo json_encode($result);
	else{
		
		echo '{
		    "sEcho": 1,
		    "iTotalRecords": "0",
		    "iTotalDisplayRecords": "0",
		    "aaData": []
		}';							
	}
}

if(isset($_POST['getPrio'])){

	$result = array();
	
	$result = $DBhandler->getPrio();
		
	if($result)
		echo json_encode($result);
	else{
		
		echo '{
		    "sEcho": 1,
		    "iTotalRecords": "0",
		    "iTotalDisplayRecords": "0",
		    "aaData": []
		}';							
	}
}

if(isset($_POST['getCategory'])){

	$result = array();
	
	$result = $DBhandler->getCategory();
		
	if($result)
		echo json_encode($result);
	else{
		
		echo '{
		    "sEcho": 1,
		    "iTotalRecords": "0",
		    "iTotalDisplayRecords": "0",
		    "aaData": []
		}';							
	}
}



if(isset($_POST['getTickets'])){

	$userID = $_SESSION['userID'];
	
	$result = array();
	
	$result = $DBhandler->getTickets($userID);
	
	if($result)
		echo json_encode($result);
	else{
		
		echo '{
		    "sEcho": 1,
		    "iTotalRecords": "0",
		    "iTotalDisplayRecords": "0",
		    "aaData": []
		}';							
	}
	
}

if(isset($_POST['getTickets2'])){

	$userID = $_SESSION['userID'];
	
	$groupID = $DBhandler->getGroupID($userID);	
	
	$result = array();
	
	$result = $DBhandler->getTickets2($groupID);

	if($result)
		echo json_encode($result);
	else{
		
		echo '{
		    "sEcho": 1,
		    "iTotalRecords": "0",
		    "iTotalDisplayRecords": "0",
		    "aaData": []
		}';							
	}
	
}

if(isset($_POST['createTicket'])){

	$data = array();
	
	$userID = $_POST['userID']; 		
	$mailID = $_POST['mailID']; 	
	
	$data = $DBhandler->getMailboxSettings($userID);

	$mailbox = $data[2];
	$mailboxUser = $data[6];
/* 	$mailboxPassword = $data[7];	 */
	$mailboxPassword = $_SESSION['mailboxPassword'];
	
	$inbox = @imap_open($mailbox,$mailboxUser,$mailboxPassword);
	
	$overview = imap_fetch_overview($inbox,$mailID,0);

	$subject = iconv_mime_decode($overview[0]->subject);
	$from = split('[<>]', $overview[0]->from);	
	$content = imap_fetchbody($inbox,$mailID,"1");	

	$data = array();
	
	$data[] = date('Y-m-d h:i');	
	$data[] = $subject;	
	$data[] = quoted_printable_decode($from[1]);		
	$data[] = $content;
	$data[] = $userID;	
	$data[] = $mailID;		

	$result = $DBhandler->addTicket($data);
	
	if(!$result)
		echo "<div class='alert alert-error' style='position: relative; top: 10px;'>Error!</div>";		
	else
/* 		echo "<div class='alert alert-success' style='position: relative; top: 10px;'>Task was created</div>"; */
		echo $result;


}

if(isset($_POST['updateTicketsList'])){

	$userID = $_POST['userID']; 		
	
	$DBhandler->listTickets($userID);
}

if(isset($_POST['saveDate'])){

	$fullDate = $_POST['fullDate']; 		
	
	$_SESSION['sinceDate'] = $fullDate;
}

if(isset($_POST['deleteUser'])){

	$userID = $_POST['userID']; 		
	
	$DBhandler->deleteUser($userID);		
}

if(isset($_POST['closeTicket'])){

	$ticketID = $_POST['ticketID']; 		
	
	$DBhandler->closeTicket($ticketID);	

	if(!$result)
		echo "<div class='alert alert-error' style='position: relative; top: 10px;'>Error!</div>";		
	else
		echo "<div class='alert alert-success' style='position: relative; top: 10px;'>Task was deleted</div>";
}


if(isset($_POST['createUserModal'])){

	echo $DBhandler->createUserModal();	
}


if(isset($_POST['editUserModal'])){

	$userID = $_POST['userID']; 		
	
	echo $DBhandler->editUserModal($userID);	
}

if(isset($_POST['editUserModal2'])){

	$userID = $_POST['userID']; 		
	
	echo $DBhandler->editUserModal2($userID);	
}

if(isset($_POST['emailSetupModal'])){
	
	echo $DBhandler->emailSetupModal();	
}


 
if(isset($_POST['changePasswordModal'])){

	$userID = $_POST['userID']; 		
	
	echo $DBhandler->changePasswordModal($userID);	

}

if(isset($_POST['createGroupModal'])){

	echo $DBhandler->createGroupModal();	

}

if(isset($_POST['createStatusModal'])){

	echo $DBhandler->createStatusModal();	

}

if(isset($_POST['createPrioModal'])){

	echo $DBhandler->createPrioModal();	

}

if(isset($_POST['createCategoryModal'])){

	echo $DBhandler->createCategoryModal();	
}

if(isset($_POST['createTicketModal'])){

	$userID = $_POST['userID']; 		

	echo $DBhandler->createTicketModal($userID);	

}

if(isset($_POST['deleteGroup'])){

	$groupID = $_POST['groupID']; 		
	
	$DBhandler->deleteGroup($groupID);		
}

if(isset($_POST['editGroupModal'])){

	$groupID = $_POST['groupID']; 		
	
	echo $DBhandler->editGroupModal($groupID);	

}

if(isset($_POST['editStatusModal'])){

	$statusID = $_POST['statusID']; 		
	
	echo $DBhandler->editStatusModal($statusID);	

}

if(isset($_POST['editPrioModal'])){

	$prioID = $_POST['prioID']; 		
	
	echo $DBhandler->editPrioModal($prioID);	

}

if(isset($_POST['editCategoryModal'])){

	$categoryID = $_POST['categoryID']; 		
	
	echo $DBhandler->editCategoryModal($categoryID);	

}

if(isset($_POST['editTicketModal'])){

	$ticketID = $_POST['ticketID']; 		
	
	echo $DBhandler->editTicketModal($ticketID);	

}



if(isset($_POST['deleteStatus'])){

	$statusID = $_POST['statusID']; 		
	
	$DBhandler->deleteStatus($statusID);		
}

if(isset($_POST['deletePrio'])){

	$prioID = $_POST['prioID']; 		
	
	$DBhandler->deletePrio($prioID);		
}

if(isset($_POST['deleteCategory'])){

	$categoryID = $_POST['categoryID']; 		
	
	$DBhandler->deleteCategory($categoryID);		
}

if(isset($_POST['viewDetailModal'])){

	$userID = $_POST['userID']; 		
	$ticketID = $_POST['ticketID']; 		
	
	echo $DBhandler->viewDetailModal($ticketID,$userID);		
}

if(isset($_POST['viewDetailModal2'])){

	$groupID = $_POST['groupID']; 		
	$ticketID = $_POST['ticketID']; 		
	
	echo $DBhandler->viewDetailModal2($ticketID,$groupID);		
}

if(isset($_POST['viewDetailModal3'])){

	$ticketID = $_POST['ticketID']; 		
	
	echo $DBhandler->viewDetailModal3($ticketID);		
}

if(isset($_POST['updateTask'])){

	$data = array();
	
	$data[] = $_POST['taskID'];
	$data[] = $_POST['subject'];
	$data[] = $_POST['priority'];
	$data[] = $_POST['comment'];			
	$data[] = $_POST['description'];			
				
	echo $DBhandler->updateTask($data);	

}

if(isset($_POST['createTask'])){

	$data = array();

	$userID = $_SESSION['userID'];
	
	$data[] = date('Y-m-d');	
	$data[] = $_POST['taskSubject']; 
	$data[] = $_POST['priority']; 	
	$data[] = $_POST['comment']; 		
	$data[] = $userID; 		
		
	$result = $DBhandler->addTicket2($data);	
		
}

if(isset($_POST['createTicketsFromEmail'])){

	$mailSettings = array();

 	$mailSettings = $DBhandler->getMailboxSettings();

	$mailbox = $mailSettings[2];
	$mailboxUser = $mailSettings[6];
	$mailboxPassword = $mailSettings[7];
	
	$defaultGroup =	$mailSettings[8];
	$defaultPrio = $mailSettings[9];
	$defaultStatus = $mailSettings[10];
	$defaultCategori = $mailSettings[11];
	
	$userID = 2;	// 2 = supportbox user	 																	

	$inbox = @imap_open($mailbox,$mailboxUser,$mailboxPassword);
	
	$emails = imap_search($inbox,"UNSEEN");	

	if($emails)
	{

		foreach($emails as $emailNumber) 
		{
		
			$overview = imap_fetch_overview($inbox,$emailNumber,0);
			
			$subject = iconv_mime_decode($overview[0]->subject);
			$body = imap_fetchbody($inbox,$emailNumber,"1");	
			$header = imap_headerinfo($inbox, $emailNumber);			
			
			$array_header=$DBhandler->object_to_array($header);

			$from=$array_header['from']['0']['mailbox'] . "@" . $array_header['from']['0']['host'];

			$structure = imap_fetchstructure($inbox, $emailNumber);
			$coding = $structure->encoding;
		
			if ($coding == 0) {
				$body = quoted_printable_decode($body);
			} elseif ($coding == 1) {
			    $body = imap_8bit($body);
			} elseif ($coding == 2) {
			    $body = imap_binary($body);
			} elseif ($coding == 3) {
			    $body = imap_base64($body);
			} elseif ($coding == 4) {
			    $body = imap_qprint($body);
			} elseif ($coding == 5) {
			    $body = $body;
			}		

			$data = array();
			
			$data[] = date('Y-m-d');	
			$data[] = utf8_encode($subject);	
			$data[] = $from;		
			$data[] = $body;
			
			$data[] = $defaultGroup;
			$data[] = $defaultPrio;			
			$data[] = $defaultStatus;			
			$data[] = $defaultCategori;
			
			$data[] = $userID;
						
			$result = $DBhandler->addTicket2($data);	
		}
	}
	
	if(!$result)
		echo "<div class='alert alert-error' style='position: relative; top: 10px;'>Error!</div>";		
	else
		echo $result;

}
	
function debug($data){
	$myFile = "debug.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");
	
	
	fwrite($fh, $data);
	
	fclose($fh);
}
	


?>