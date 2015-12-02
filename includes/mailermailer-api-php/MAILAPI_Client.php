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
        $result = $this->mailapi_call->executeMethod('ping', $params);
        return $result;
    }

    /**
     * Returns the fields needed to create a list member or to create/populate a signup form.
     *
     * @return formfields_struct | MAILAPI_Error
     */
    public function getFormFields()
    {
        $params = array();
        $result = $this->mailapi_call->executeMethod('getFormFields', $params);
        return $result;
    }

    /**
     * Add a collection of members records to the account email list.
     *
     * @param array   $member a member struct
     * @param boolean $send_invite flag to send double opt-in confirmation message, defaults to true
     * @param boolean $send_welcome flag to send welcome message, defaults to false
     * @param boolean $update_existing flag to control whether existing list members should be updated rather than throwing an exception, defaults to false
     * @param boolean $enforce_required flag to control whether missing required fields as specified by account configuration should throw an exception, defaults to true
     * @return true | MAILAPI_Error
     */
    public function addBulkMembers($members, $send_invite = true, $send_welcome = false, $update_existing = false, $enforce_required = true)
    {
        $params                     = array();
        $params['members']          = php_xmlrpc_encode($members);
        $params['send_invite']      = php_xmlrpc_encode($send_invite);
        $params['send_welcome']     = php_xmlrpc_encode($send_welcome);
        $params['update_existing']  = php_xmlrpc_encode($update_existing);
        $params['enforce_required'] = php_xmlrpc_encode($enforce_required);
        $result = $this->mailapi_call->executeMethod('addBulkMembers', $params);
        return $result;
    }

    /**
     * Add the specified member record to the account email list.
     *
     * @param array   $member a member struct
     * @param boolean $send_invite flag to control if double opt-in confirmation message is sent, defaults to true
     * @param boolean $send_welcome flag to send welcome message, defaults to false
     * @param boolean $update_existing flag to control whether existing list members should be updated rather than throwing an exception, defaults to false
     * @param boolean $enforce_required flag to control whether missing required fields as specified by account configuration should throw an exception, defaults to true
     * @return true | MAILAPI_Error
     */
    public function addMember($member, $send_invite = true, $send_welcome = false, $update_existing = false, $enforce_required = true)
    {
        $params                     = array();
        $params['member']           = php_xmlrpc_encode($member);
        $params['send_invite']      = php_xmlrpc_encode($send_invite);
        $params['send_welcome']     = php_xmlrpc_encode($send_welcome);
        $params['update_existing']  = php_xmlrpc_encode($update_existing);
        $params['enforce_required'] = php_xmlrpc_encode($enforce_required);
        $result = $this->mailapi_call->executeMethod('addMember', $params);
        return $result;
    }

    /**
     * Unsubscribe a collection of member email addresses from the account list.
     *
     * @param string $user_emails emails of the members to unsubscribe
     * @return true | MAILAPI_Error
     */
    public function unsubBulkMembers($user_emails)
    {
        $params                 = array();
        $params['user_emails']   = php_xmlrpc_encode($user_emails);
        $result = $this->mailapi_call->executeMethod('unsubBulkMembers', $params);
        return $result;
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
        $result = $this->mailapi_call->executeMethod('unsubMember', $params);
        return $result;
    }

    /**
     * Suppress the member email address.
     *
     * @param string $user_email email of the member to suppress
     * @return true | MAILAPI_Error
     */
    public function suppressMember($user_email)
    {
        $params                 = array();
        $params['user_email']   = php_xmlrpc_encode($user_email);
        $result = $this->mailapi_call->executeMethod('suppressMember', $params);
        return $result;
    }

    /**
     * Unsuppress the member email address.
     *
     * @param string $user_email email of the member to unsuppress
     * @return true | MAILAPI_Error
     */
    public function unsuppressMember($user_email)
    {
        $params                 = array();
        $params['user_email']   = php_xmlrpc_encode($user_email);
        $result = $this->mailapi_call->executeMethod('unsuppressMember', $params);
        return $result;
    }
}

?>
