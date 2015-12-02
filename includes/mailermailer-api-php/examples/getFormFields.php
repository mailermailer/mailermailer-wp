<?php

include("../MAILAPI_Client.php");

// Make sure we have an api key
if (getenv('MAILAPI_KEY') == null) {
  exit('Set setenv("MAILAPI_KEY") to use this example');
}

// Create our API object
$mailapi = new MAILAPI_Client(getenv('MAILAPI_KEY'));

// Get form fields
$response = $mailapi->getFormFields();

// Evaluate response
if (MAILAPI_Error::isError($response)) {
    echo "Error \n";
    echo "Code: " . $response->getErrorCode() . "\n";
    echo "Message: ". $response->getErrorMessage() . "\n";
} else {
    echo "Success\n";
    foreach ($response as $formfield) {
        echo "Fieldname: " . $formfield["fieldname"] . "\n";
        echo "Description:" . $formfield["description"] . "\n";
        echo "Type: " . $formfield["type"] . "\n\n";
    }
}

?>
