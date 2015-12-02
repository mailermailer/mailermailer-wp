<?php

require_once("test_config.php");

class AddMember extends PHPUnit_Framework_TestCase
{
    protected $mailapi;
    protected $email;
    protected $member;

    protected function setUp()
    {
        include 'test_vars.php';

        $this->mailapi = new MAILAPI_Client($test_apikey);

        $this->email = $test_email1;

        $formfields = $this->mailapi->getFormFields();

        foreach ($formfields as $formfield) {
            $value;
            if ($formfield["type"] == 'select') {
               if ($formfield["attributes"]["select_type"] == 'multi') {
                    $value = array("a","b","c","d");            
                } else {
                    $value = array("a");
                }
            } elseif ($formfield["type"] == 'open_text') {
                $value = "ANYTHING";
            } elseif ($formfield["type"] == 'state') {
                $value = "md";
            } elseif ($formfield["type"] == 'country') {
                $value = "us";
            }
            $this->member[$formfield["fieldname"]] = $value;
        }
        
        $this->member["user_email"] = $this->email;

        sleep(1);
    }

    public function testSuccess()
    {
        $response = $this->mailapi->addMember($this->member);
        $this->assertEquals(1, $response);
    }

    public function testEmptyMember()
    {
        $response = $this->mailapi->addMember(array());
        $this->assertInstanceOf('MAILAPI_Error', $response);
        $this->assertEquals(301, $response->getErrorCode());
    }

    public function testInvalidInput()
    {
        $this->member["user_email"] = "asdfasdf";
        $response = $this->mailapi->addMember($this->member);
        $this->assertInstanceOf('MAILAPI_Error', $response);
        $this->assertEquals(302, $response->getErrorCode());

        $this->member["user_email"] = "asdfasdf@asdfasdfas";
        $response = $this->mailapi->addMember($this->member);
        $this->assertInstanceOf('MAILAPI_Error', $response);
        $this->assertEquals(302, $response->getErrorCode());
    }

    public function testDuplicate()
    {
        $response = $this->mailapi->addMember($this->member);

        $response2 = $this->mailapi->addMember($this->member);
        $this->assertInstanceOf('MAILAPI_Error', $response2);
        $this->assertEquals(304, $response2->getErrorCode());        
    }

    public function testEnableInviteAndWelcome()
    {
        $response = $this->mailapi->addMember($this->member,true,true);
        $this->assertInstanceOf('MAILAPI_Error', $response);
        $this->assertEquals(102, $response->getErrorCode());
    }

    public function testDisableEnforceRequired()
    {
        //make sure user_fname is a required field in your testing environment
        $this->member["user_fname"] = "";
        $response = $this->mailapi->addMember($this->member,true,false,false,false);
        $this->assertEquals(1, $response);
    }

    public function testMissingFields()
    {
        //make sure user_fname is a required field in your testing environment
        $this->member["user_fname"] = "";
        $response = $this->mailapi->addMember($this->member);
        $this->assertEquals(301, $response->getErrorCode()); 
    }
}
?>