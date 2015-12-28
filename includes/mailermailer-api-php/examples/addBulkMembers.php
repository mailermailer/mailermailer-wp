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

$member1 = array();
$member2 = array();

// Generate two different emails
$test_email1 = str_replace("@", "+php1@", getenv('MAILAPI_TEST_EMAIL'));
$test_email2 = str_replace("@", "+php2@", getenv('MAILAPI_TEST_EMAIL'));

// member1
$member1['user_email'] = $test_email1;
$member1['user_fname'] = 'John';
$member1['user_lname'] = 'Doe';

// member2
$member2['user_email'] = $test_email2;
$member2['user_fname'] = 'Obi-Wan';
$member2['user_lname'] = 'Kenobi';

// Create our API object
$mailapi = new MAILAPI_Client(getenv('MAILAPI_KEY'));

// Add members
$members = array($member1, $member2);
$report = $mailapi->addBulkMembers($members);

// Evaluate response
echo "Number of added members: " . $report["added"] . "\n";
echo "Number of updated members: " . $report["updated"] . "\n";
foreach ($report["errors"] as $error) {
  echo "Email that caused the error: " . $error["email"] . "\n";
  echo "Message: " . $error["error_message"] . "\n";
  echo "Code: " . $error["error_code"] . "\n";
}

?>
