<?php

/**
 * Refer to license.php for file headers and license
 */

require_once('MAILAPI_Call.php');

/**
 * Class that implements all the method calls available through
 * the Mail API.
 */
class MAILAPI_Client
{

    private $mailapi_call;

    public function __construct($apikey)
    {
        $this->mailapi_call = new MAILAPI_Call($apikey);
    }

    /**
     * Ping the Mail API. This simple method will return "true"
     * if you can connect with the API, or an exception if you cannot.
     *
     * @return true | MAILAPI_Error
     */
    public function ping()
    {
        $params = array();
        $response = $this->mailapi_call->executeMethod('ping', $params);
        return MAILAPI_Client::getResult($response);
    }

    /**
     * Returns the fields needed to create a list member or to create/populate a signup form.
     *
     * @return formfields_struct | MAILAPI_Error
     */
    public function getFormFields()
    {
        $params = array();
        $response = $this->mailapi_call->executeMethod('getFormFields', $params);
        return MAILAPI_Client::getResult($response);
    }

    /**
     * Add the specified member record to the account email list.
     *
     * @param array   $member a member struct
     * @param boolean $send_invite flag to send double opt-in confirmation message, defaults to true
     * @param boolean $send_welcome flag to send welcome message, defaults to false
     * @return true | MAILAPI_Error
     */
    public function addMember($member, $send_invite = true, $send_welcome = false)
    {
        $params                   = array();
        $params['member']         = php_xmlrpc_encode($member);
        $params['send_invite']    = php_xmlrpc_encode($send_invite);
        $params['send_welcome']   = php_xmlrpc_encode($send_welcome);
        $response = $this->mailapi_call->executeMethod('addMember', $params);
        return MAILAPI_Client::getResult($response);
    }

    /**
     * Unsubscribe the email address from the account email list.
     *
     * @param string $user_email email of the member to unsubscribe
     * @return true | MAILAPI_Error
     */
    public function unsubMember($user_email)
    {
        $params                 = array();
        $params['user_email']   = php_xmlrpc_encode($user_email);
        $response = $this->mailapi_call->executeMethod('unsubMember', $params);
        return MAILAPI_Client::getResult($response);
    }

    /**
     * Formats the response as necessary.
     *
     * @param  mixed $response xmlrpc encoded response from server
     * @return mixed
     * @static
     */
    static function getResult($response)
    {        
        if (!MAILAPI_Error::isError($response)) {
            return php_xmlrpc_decode($response);
        } else {
            return $response;
        }
    }
}


?>