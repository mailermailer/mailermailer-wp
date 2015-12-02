<?php

require_once("test_config.php");

class UnsubMember extends PHPUnit_Framework_TestCase
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

        $response = $this->mailapi->addMember($this->member);
        $this->assertEquals(1, $response);
        
        sleep(1);
    }

    public function testSuccess()
    {
        $response = $this->mailapi->unsubMember($this->email);
        $this->assertEquals(1, $response);
    }

    public function testUnsubTwice()
    {
        $response = $this->mailapi->unsubMember($this->email);
        $this->assertEquals(1, $response);

        $response = $this->mailapi->unsubMember($this->email);
        $this->assertEquals(112, $response->getErrorCode());   
    }

    public function testUnsubNonexistentListMember()
    {
        $response = $this->mailapi->unsubMember("This email doesn't exist");
        $this->assertEquals(305, $response->getErrorCode());        
    }

    public function testMissingFields()
    {
        $response = $this->mailapi->unsubMember("");
        $this->assertEquals(301, $response->getErrorCode());   
    }

    public function testUnsubResub()
    {
        $response = $this->mailapi->unsubMember($this->email);
        $this->assertEquals(1, $response);

        $response = $this->mailapi->addMember($this->member);
        $this->assertEquals(112, $response->getErrorCode());
    }
}
?>