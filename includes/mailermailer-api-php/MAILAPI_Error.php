<?php

/**
 * Refer to license.php for file headers and license
 */

/**
 * Class that encapsulates errors that are returned from the Mail API
 */
class MAILAPI_Error
{

    private $errorCode;
    private $errorMessage;
  
    public function __construct($errorCode, $errorMessage)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }
    
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    static function isError($MAILAPI_OBJECT)
    {
        if ($MAILAPI_OBJECT instanceof MAILAPI_Error) {
            return true;
        }
        return false;
    }
}

?>