<?php

if (getenv('MAILAPI_KEY') == null) {
  exit("Provide your APIKEY by setting MAILAPI_KEY env variable.\n");
}

if (getenv('MAILAPI_URL') == null) {
  exit("Provide the API endpoint by setting MAILAPI_URL env variable.\n");
}

if (getenv('MAILAPI_TEST_EMAIL') == null) {
  exit("Provide the test email address by setting MAILAPI_TEST_EMAIL env variable.\n");
}

$test_apikey = getenv('MAILAPI_KEY');

$test_email1 = str_replace("@", "+php1" . md5(time()) . "@", getenv('MAILAPI_TEST_EMAIL'));
$test_email2 = str_replace("@", "+php2" . md5(time()) . "@", getenv('MAILAPI_TEST_EMAIL'));
$test_email3 = str_replace("@", "+php3" . md5(time()) . "@", getenv('MAILAPI_TEST_EMAIL'));

?>