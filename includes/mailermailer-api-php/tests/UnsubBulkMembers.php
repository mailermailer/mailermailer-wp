<?php

require_once("test_config.php");

class UnsubBulkMembers extends PHPUnit_Framework_TestCase
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

        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member2, $this->member3));

        $expected = array('added' => 3, 'updated' => 0, 'errors' => array(), 'report' => $response);
        $this->assertEquals(1, $this->checkAddReport($expected));
        
        sleep(1);
    }

    public function testSuccess()
    {
        $response = $this->mailapi->unsubBulkMembers(array($this->email1, $this->email2, $this->email3));
        $expected = array('unsubscribed' => 3, 'errors' => array(), 'report' => $response);
        $this->assertEquals(1, $this->checkUnsubReport($expected));
    }

    public function testEmptyList()
    {
        $response = $this->mailapi->unsubBulkMembers(array());
        $this->assertInstanceOf('MAILAPI_Error', $response);
        $this->assertEquals(301, $response->getErrorCode());
    }

    public function testUnsubNonexistentListMembers()
    {
        $response = $this->mailapi->unsubBulkMembers(array('dontexist', 'fake@email.com', 'er-mer-gerd'));
        $expected = array('unsubscribed' => 0, 'errors' => array('dontexist' => 1, 'fake@email.com' => 1, 'er-mer-gerd' => 1), 'report' => $response);
        $this->assertEquals(1, $this->checkUnsubReport($expected));

        $response = $this->mailapi->unsubBulkMembers(array('', '', ''));
        $this->assertInstanceOf('MAILAPI_Error', $response);
        $this->assertEquals(301, $response->getErrorCode());
    }

    public function testUnsubTwice()
    {
        $response = $this->mailapi->unsubBulkMembers(array($this->email1, $this->email2, $this->email3));
        $expected = array('unsubscribed' => 3, 'errors' => array(), 'report' => $response);
        $this->assertEquals(1, $this->checkUnsubReport($expected));

        $response = $this->mailapi->unsubBulkMembers(array($this->email1, $this->email2, $this->email3));
        $expected = array('unsubscribed' => 0, 'errors' => array($this->email1 => 1, $this->email2 => 1, $this->email3 => 1), 'report' => $response);
        $this->assertEquals(1, $this->checkUnsubReport($expected))
        ; 
    }

    public function testUnsubResub()
    {
        $response = $this->mailapi->unsubBulkMembers(array($this->email1, $this->email2, $this->email3));
        $expected = array('unsubscribed' => 3, 'errors' => array(), 'report' => $response);
        $this->assertEquals(1, $this->checkUnsubReport($expected));

        $response = $this->mailapi->addBulkMembers(array($this->member1, $this->member2, $this->member3));
        $expected = array('added' => 0, 'updated' => 0, 'errors' => array($this->email1 => 1, $this->email2 => 1, $this->email3 => 1), 'report' => $response);
        $this->assertEquals(1, $this->checkAddReport($expected));
    }

    ///////////////////////////////////////////////////////////////
    //
    // HELPER FUNCTION
    //
    ////////////////////////////////////////////////////////////////
    public function checkAddReport($params)
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

    public function checkUnsubReport($params)
    {
        $unsubscribed = $params["unsubscribed"];
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

        return ($unsubscribed == $report["unsubscribed"] && count($errors) == 0);
    }
}
?>