<?php

// The full url to your switchboard installation. No trailing slash.
define("HOST", "http://example.com/switchboard");

// database
define("DB_HOST", "localhost");
define("DB_USER", "some_user_name");
define("DB_PASS", "some_secret_pass");
define("DB_NAME", "some_db_name");
$TABLE_NAME = "switchboard"; // Should not need to change this.

// Twilio AccountSid and AuthToken from www.twilio.com/user/account
//$AccountSid = "TWILIO_SECRET";
//$AuthToken = "TWILIO_SECRET";

$form_prompt = "Prompt"; // TEMPORARY - will change this to user-editable in settings screen

/// Should not need to change anything below this line. ///

// The full path to your installation. 
define("BASEPATH", dirname(__FILE__));

// Setup php for working with Unicode data.
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');

?>