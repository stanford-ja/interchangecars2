<?php
class Mailer {
	// Retreives a single record where id=$i
	var $email = "MRICC@yahoogroups.com"; // Send email to
	var $headers = "From: mricf@stanfordhosting.net"; // Headers for email
	var $subject = ""; // Subject of email
	var $body = ""; // Body of email

	function __construct(){
		// method to run when class created.
	}
	
	function setEmail($e=""){
		if(strlen($e) > 0){$this->email = $e;}
	}
	
	function setHeaders($h=""){
		if(strlen($h) > 0){$this->headers = $h;}
	}
	
	function setSubject($s=""){
		if(strlen($s) > 0){$this->subject = $s;}
	}
	
	function setBody($b=""){
		if(strlen($b) > 0){$this->body = $b;}
	}
	
	function send(){
		$mess = "There was a problem sending the email.<br /><br />";
		if(mail($this->email, $this->subject, $this->body, $this->headers)){
			$mess = "Email successfully sent with subject of <strong>".$this->subject."</strong>";
		}
		echo $mess;
	}
}
?>