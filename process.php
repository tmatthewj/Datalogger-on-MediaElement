<?php

$process = new process_coming_soon();

//Set the registering email
$process->from_email = $_POST['email'];
$process->from_name = $_POST['name'];
$process->to = "matthew@vdohive.com";
$process->process();

class process_coming_soon {
	
	public $to = false;
	
	public $from_email = false;
	public $from_name = false;
	
	/**
	 * Class Constructor
	 */
	public function __construct(){
		//do nothing.
	}
	
	/**
	 * Decides if we send an email to the user or stores the email in the database
	 */
	public function process(){
		$return = $this->send_email();
		if(!$return){
			echo 'failed';		
		}
	}
	
	
	/**
	 * Sends the email of the signup
	 * 
	 * @returns
	 * success or failed
	 */
	private function send_email(){
		$subject = "New User sign-up email";
		$body = $this->from_name." has submitted his/her email address on your coming soon web page. The email address is ".$this->from_email.".";
		 
		if (mail($this->to, "Signup alert", $body)) {
		   return true;
		} else {
		}
	}
	
}
?>