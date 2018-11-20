
<?php

/* session_start(); */

	
	include('include/DBHandler.php');
	
	$DBhandler = new DBHandler();


		$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
		$username = 'spformbox@gmail.com';
		$password = 'Spform1234!';

		
		$inbox = @imap_open($hostname,$username,$password);
		
		$emails = imap_search($inbox,"SINCE 2014-01-01");	

		foreach($emails as $email_number) 
		{
		
			$overview = imap_fetch_overview($inbox,$email_number,0);			
			$subject = iconv_mime_decode($overview[0]->subject);
			$from = iconv_mime_decode($overview[0]->from);	
			$content = imap_fetchbody($inbox,$email_number,"1");	
			
			
			echo "<pre>";
			print_r($overview);
			echo "</pre>";
			echo "Subject: ".quoted_printable_decode($subject);
			echo "<br>from: ".quoted_printable_decode($from);
			echo "<br>content: ".quoted_printable_decode($content);
			echo "<hr>";


		}



/*
	$mailSettings = array();

 	$mailSettings = $DBhandler->getMailboxSettings();

	$mailbox = $mailSettings[2];
	$mailboxUser = $mailSettings[6];
	$mailboxPassword = $mailSettings[7];
	
	$defaultGroup =	$mailSettings[8];
	$defaultPrio = $mailSettings[9];
	$defaultStatus = $mailSettings[10];
	$defaultCategori = $mailSettings[11]; 																	

	$inbox = @imap_open($mailbox,$mailboxUser,$mailboxPassword);
	
	$emails = imap_search($inbox,"UNSEEN");	

	if($emails)
	{

		foreach($emails as $emailNumber) 
		{
		
			$overview = imap_fetch_overview($inbox,$emailNumber,0);		
		
			$subject = iconv_mime_decode($overview[0]->subject);
			$content = imap_fetchbody($inbox,$emailNumber,"1");	
			$header = imap_headerinfo($inbox, $emailNumber);			
			
			$array_header=$DBhandler->object_to_array($header);

			$from=$array_header['from']['0']['mailbox'] . "@" . $array_header['from']['0']['host'];

			$data = array();
			
			$data[] = date('Y-m-d h:i');	
			$data[] = $subject;	
			$data[] = $from;		
			$data[] = $content;
			
			$data[] = $defaultGroup;
			$data[] = $defaultPrio;			
			$data[] = $defaultStatus;			
			$data[] = $defaultCategori;
						
			$result = $DBhandler->addTicket2($data);	
		}
	}
*/
	
/*
	if(!$result)
		echo "<div class='alert alert-error' style='position: relative; top: 10px;'>Error!</div>";		
	else
		echo $result;
*/

?>
