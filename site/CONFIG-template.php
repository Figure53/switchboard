<?php

// The full url to your switchboard installation. No trailing slash.
define("HOST", "http://example.com/switchboard");

// database
define("DB_HOST", "localhost");
define("DB_USER", "some_user_name");
define("DB_PASS", "some_secret_pass");
define("DB_NAME", "some_db_name");
$TABLE_NAME = "switchboard"; // Should not need to change this.

// The full path to your installation. Should not need to change this.
define("BASEPATH", dirname(__FILE__));

// Twilio AccountSid and AuthToken from www.twilio.com/user/account
//$AccountSid = "TWILIO_SECRET";
//$AuthToken = "TWILIO_SECRET";

?>