<?php

require_once("test_config.php");

class Ping extends PHPUnit_Framework_TestCase
{
    protected $mailapi;

    protected function setUp()
    {
        include 'test_vars.php';

        $this->mailapi = new MAILAPI_Client($test_apikey);
    }

    public function testSuccess()
    {
        $response = $this->mailapi->ping();
        $this->assertEquals(1, $response);
    }

    public function testBadAPIKey()
    {
        $this->mailapi = new MAILAPI_Client('invalid_api_key');
        $response = $this->mailapi->ping();
        $this->assertTrue(MAILAPI_Error::isError($response));
        $this->assertEquals(11003, $response->getErrorCode());
    }
}
?>