<?php

require_once("test_config.php");

class AddBulkMembers extends PHPUnit_Framework_TestCase
{
    protected $mailapi;

    protected $email1;
    protected $email2;
    protected $email3;

    protected $member1;
    protected $member2;
    protected $member3;

    protected function setUp()
    {
        include 'test_vars.php';

        $this->mailapi = new MAILAPI_Client($test_apikey);

        $this->email1 = $test_email1;
        $this->email2 = $test_email2;
        $this->email3 = $test_email3;

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
            $this->member1[$formfield["fieldname"]] = $value;
            $this->member2[$formfield["fieldname"]] = $value;
            $this->member3[$formfield["fieldname"]] = $value;
        }
        
        $this->member1["user_email"] = $this->email1;
        $this->member2["user_email"] = $this->email2;
        $this->member3["user_email"] = $this->email3;

        sleep(1);
    }

    public function testSuccess()
    {
        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member2, $this->member3));
        $expected = array('added' => 3, 'updated' => 0, 'errors' => array(), 'report' => $response);
        $this->assertEquals(1, $this->checkReport($expected));
    }

    public function testUpdateExistingDefault()
    {
        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member1, $this->member1));
        $expected = array('added' => 1, 'updated' => 0, 'errors' => array($this->email1 => 2), 'report' => $response);
        $this->assertEquals(1, $this->checkReport($expected));
    }

    public function testUpdateExistingTrue()
    {
        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member1, $this->member1), true, false, true);
        $expected = array('added' => 1, 'updated' => 2, 'errors' => array(), 'report' => $response);
        $this->assertEquals(1, $this->checkReport($expected));
    }

    public function testEmptyList()
    {
        $response = $this->mailapi->addBulkMembers(array());
        $this->assertInstanceOf('MAILAPI_Error', $response);
        $this->assertEquals(301, $response->getErrorCode());
    }

    public function testEnableInviteAndWelcome()
    {
        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member1, $this->member1), true, true);
        $this->assertInstanceOf('MAILAPI_Error', $response);
        $this->assertEquals(102, $response->getErrorCode());
    }

    public function testMissingEmails()
    {
        $this->member1["user_email"] = '';
        $this->member2["user_email"] = '';
        $this->member3["user_email"] = '';

        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member2, $this->member3));
        $expected = array('added' => 0, 'updated' => 0, 'errors' => array('missing/invalid email' => 3), 'report' => $response);
        $this->assertEquals(1, $this->checkReport($expected));
    }

    public function testMalformedEmails()
    {
        $this->member1["user_email"] = 'asdfasdf';
        $this->member2["user_email"] = 'asdf@sd.com';
        $this->member3["user_email"] = 'asdfasdf';

        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member2, $this->member3));
        $expected = array('added' => 0, 'updated' => 0, 'errors' => array('asdfasdf' => 2, 'asdf@sd.com' => 1), 'report' => $response);
        $this->assertEquals(1, $this->checkReport($expected));
    }

    public function testDisableEnforceRequired()
    {
        //make sure user_fname is a required field in your testing environment
        $this->member1["user_fname"] = '';
        $this->member2["user_fname"] = '';
        $this->member3["user_fname"] = '';

        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member2, $this->member3),true,false,false,false);
        $expected = array('added' => 3, 'updated' => 0, 'errors' => array(), 'report' => $response);
        $this->assertEquals(1, $this->checkReport($expected));
    }

    public function testMissingFields()
    {
        //make sure user_fname is a required field in your testing environment
        $this->member1["user_fname"] = '';
        $this->member2["user_fname"] = '';
        $this->member3["user_fname"] = '';
        
        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member2, $this->member3));
        $expected = array('added' => 0, 'updated' => 0, 'errors' => array($this->member1["user_email"] => 1, $this->member2["user_email"] => 1, $this->member3["user_email"] => 1), 'report' => $response);
        $this->assertEquals(1, $this->checkReport($expected));
    }
    
    ///////////////////////////////////////////////////////////////
    //
    // HELPER FUNCTION
    //
    ////////////////////////////////////////////////////////////////
    public function checkReport($params)
    {
        $added = $params["added"];
        $updated = $params["updated"];
        $errors = $params["errors"];

        $report = $params["report"];
        
        if (count($errors)) {
            foreach ($report["errors"] as $value) {
                if ($errors[$value["email"]]) {
                    if ($errors[$value["email"]] > 1) {
                        $errors[$value["email"]]--;
                    }
                    else {
                        unset($errors[$value["email"]]);
                    }
                }
            }
        }
        return ($added == $report["added"] && $updated == $report["updated"] && count($errors) == 0);
    }
}
?>