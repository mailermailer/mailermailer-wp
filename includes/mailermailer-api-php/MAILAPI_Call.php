<?php

/**
 * Refer to license.php for file headers and license
 */

require_once('xmlrpc/xmlrpc.inc');
require_once('config.php');
require_once('MAILAPI_Error.php');

/**
 *  Override the default internal encoding declared within xmlrpc.inc
 */
$xmlrpc_internalencoding = "UTF-8";

/**
 * Class that performs all the required work to
 * connect to the Mail API.
 */
class MAILAPI_Call
{

    private $apikey;

    public function __construct($apikey)
    {
        $this->apikey = $apikey;
    }

    /**
     * Connects to the Mail API and calls the desired
     * function with the specified parameters
     * 
     * @param  method to invoke and parameters for the method
     * @return mixed
     */
    public function executeMethod($method, $params)
    {
        $host = getenv("MAILAPI_URL") ? getenv("MAILAPI_URL") : MAILAPI_ENDPOINT;

        $params['apikey'] = new xmlrpcval($this->apikey);

        $xmlrpcmsg = new xmlrpcmsg($method, array(new xmlrpcval($params, 'struct')));

        $xmlrpc_client = new xmlrpc_client($host);
        $xmlrpc_client->request_charset_encoding="UTF-8";
        $xmlrpc_client->SetUserAgent(MAILAPI_PARTNER . "/PHP/v" . MAILAPI_VERSION);

        $response = $xmlrpc_client->send($xmlrpcmsg);

        if (!$response->faultCode()) {
           return php_xmlrpc_decode($response->value());
        } else {
            return new MAILAPI_Error($response->faultCode(), $response->faultString());
        }
    }
}

?>
