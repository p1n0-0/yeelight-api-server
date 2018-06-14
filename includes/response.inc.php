<?php

##
## Yeelight-API-Server Responses
##

# Setting no-cache & content-type to application/json
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json');

# Class response
class Response {

    public $success = false;
    public $data = [];
    public $error = null;

    function __construct($success = true, $data = null, $error = null) {

        $this->success = $success;

        if ($data == null)
            unset($this->data);
        else
            $this->data = $data;

        if ($error == null)
            unset($this->error);
        else
            $this->error = $error;

    }

}

# Class response error
class ResponseError {

    public $code = 400;
    public $message = "Unknown error";

    function __construct($code = 400, $message = "Unknown error") {

        $this->code = $code;
        $this->message = $message;

    }

}

# Function to return ok
function return_ok($data = null) {

    // Ignore user abort, set time limit to 0 & ob_start
    ignore_user_abort(true);
    set_time_limit(0);
    ob_start();

    // Response
    http_response_code (200);
    echo json_encode(new Response(true, $data), JSON_PRETTY_PRINT);

    // Close connection to continue processing
    header('Connection: close');
    header('Content-Length: '.ob_get_length());
    ob_end_flush();
    ob_flush();
    flush();

}

# Function to return error
function return_error($code = 400, $message = "Unknown error") {

    // Response
    http_response_code($code);
    echo json_encode(new Response(false, null, new ResponseError($code, $message)), JSON_PRETTY_PRINT);

}
