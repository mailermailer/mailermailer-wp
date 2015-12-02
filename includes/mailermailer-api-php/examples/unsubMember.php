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

// Create our API object
$mailapi = new MAILAPI_Client(getenv('MAILAPI_KEY'));

// Unsubscribe list member
$response = $mailapi->unsubMember(getenv('MAILAPI_TEST_EMAIL'));

// Evaluate response
if (MAILAPI_Error::isError($response)) {
    echo "Error \n";
    echo "Code: " . $response->getErrorCode() . "\n";
    echo "Message: ". $response->getErrorMessage() . "\n";
} else {
    echo "Success\n";
}

?>
