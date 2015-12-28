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

$member = array();

// Open text fields
$member['user_email'] = getenv('MAILAPI_TEST_EMAIL');
$member['user_fname'] = 'John';
$member['user_lname'] = 'Doe';

// Country
$member['user_country'] = 'us';

// State
$member['user_state'] = 'md';

// Category fields with multiple selection (checkboxes)
$member['user_attr1'] = array('a','b','c','d');

// Category fields with single selection (dropdown menu)
$member['user_attr2'] = array('a');

// Create our API object
$mailapi = new MAILAPI_Client(getenv('MAILAPI_KEY'));

// Add the member
$response = $mailapi->addMember($member);

// Evaluate response
if (MAILAPI_Error::isError($response)) {
    echo "Error \n";
    echo "Code: " . $response->getErrorCode() . "\n";
    echo "Message: ". $response->getErrorMessage() . "\n";
} else {
    echo "Success added member\n";
}

?>
