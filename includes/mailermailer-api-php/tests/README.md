# PHP API wrapper tests

A simple test suite written in [PHPUnit](http://phpunit.de/manual/3.7/en/index.html) to test the basic functionality of the API.

## Warning

These tests are for development purposes only. If you are not a developer, please __DO NOT__ run these tests with your account information. 

## Requirements

### Software

- PHP 5
- PHPUnit test framework

### Variables

Before running the tests, make sure you have set the following environmental variables:

- MAILAPI_KEY = test apikey
- MAILAPI_URL = test url endpoint
- MAILAPI_TEST_EMAIL = test email address

## Installation

To install PHPUnit please follow the directions provided [here](http://phpunit.de/manual/3.7/en/installation.html).

## Usage

To run a single test script, navigate to the 'tests' directory and use the phpunit command with the test you desire.

    > phpunit PingTest.php

To run an individual test case within the test script, use the following:

    > phpunit --filter testSuccess PingTest.php

To run the whole test suite, navigate to the parent directory and input the following:

    > phpunit tests/

For more information on how to run tests, please refer to the offical PHPUnit manual [here](http://phpunit.de/manual/3.7/en/textui.html).