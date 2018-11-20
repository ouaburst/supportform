<?php

/* echo gethostbyname("imap.gmail.com"); */
// 173.194.71.108
/* 	$hostname = '{173.194.71.108:993/imap/ssl}INBOX'; */

/*
	$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
	$username = 'sacke01@gmail.com';
	$password = 'banan123';
*/

/* 	$hostname = '{mail.obkonsult.com:143/imap/tls/novalidate-cert}INBOX';	 */
	$hostname = '{mail.obkonsult.com:143/imap/notls}INBOX';	

	$username = 'ob@obkonsult.com';
	$password = 'taher2007';

	$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
	
	$emails = imap_search($inbox,"SINCE 2013-10-10");	

/*
	if($emails) {
	
		rsort($emails);																			

		foreach($emails as $email_number) {
	
			
			$overview = imap_fetch_overview($inbox,$email_number,0);

			$message = imap_fetchbody($inbox,$email_number,"1");

//echo "<pre>";
//print_r($overview);
//echo "</pre>";
//echo "--<br>".$message;
//echo "<hr>";

			$subject = iconv_mime_decode($overview[0]->subject);
			//$from = split('[<>]', $overview[0]->from);
			$from = $overview[0]->from;
			$emailID = $overview[0]->msgno;
			$date = date('Y-m-d',strtotime(strip_tags($overview[0]->date)));			
			
			echo "subject: ".$subject."<br>";
			echo "from: ".$from."<br>";
			echo "emailID: ".$emailID."<br>";
			echo "date: ".$date."<br>";									
			echo "<hr>";
			
	   }     

	}
*/

	if($emails) 
	{		
		$array = array();
		$output = array( "aaData" => array());						
		
		rsort($emails);								
		
		foreach($emails as $email_number) 
		{
			$content = array();		
			$overview = imap_fetch_overview($inbox,$email_number,0);
			$subject = iconv_mime_decode($overview[0]->subject);
/* 			$from = split('[<>]', $overview[0]->from); */
			$from = $overview[0]->from;
			$emailID = $overview[0]->msgno;
			$date = date('Y-m-d',strtotime(strip_tags($overview[0]->date)));			
			
			$content[] = (string)$overview[0]->msgno;											
			$content[] = utf8_encode($subject);
/* 			$content[] = quoted_printable_decode($from[1]); */
			$content[] = $from;
			$content[] = $date;
			$content[] = "";

			$output['aaData'][] = $content;
		}
		
		imap_close($inbox);	

		echo json_encode($output);			
	}     

		
/* 	imap_close($inbox);		 */

?>