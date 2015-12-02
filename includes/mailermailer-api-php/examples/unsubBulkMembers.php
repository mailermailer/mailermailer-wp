<?php

include("../MAILAPI_Client.php");

// Make sure we have an api key
if (getenv('MAILAPI_KEY') == null) {
  exit('Set setenv("MAILAPI_KEY") to use this example');
}

// Make sure we have an email address
if (getenv('MAILAPI_TEST_EMAIL') == null) {
  exit('Set setenv("MAILAPI_TEST_EMAIL") to use this example');
}

// Generate two different emails
$test_email1 = str_replace("@", "+php1@", getenv('MAILAPI_TEST_EMAIL'));
$test_email2 = str_replace("@", "+php2@", getenv('MAILAPI_TEST_EMAIL'));

// Create our API object
$mailapi = new MAILAPI_Client(getenv('MAILAPI_KEY'));

// Unsubscribe list members
$user_emails = array($test_email1, $test_email2);
$report = $mailapi->unsubBulkMembers($user_emails);

// Evaluate response
echo "Number of unsubscribed memebers: " . $report["unsubscribed"] . "\n";
foreach ($report["errors"] as $error) {
  echo "Email that caused the error: " . $error["email"] . "\n";
  echo "Message: " . $error["error_message"] . "\n";
  echo "Code: " . $error["error_code"] . "\n";
}

?>
