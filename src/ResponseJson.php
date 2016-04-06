<?php

namespace DigitalHammer\LpForms;

class ResponseJson
{
    private $responseCode;

    public function __construct($responseCode = 200)
    {
        $this->responseCode = $responseCode;
    }

    public function make($data)
    {
        http_response_code($this->responseCode);
        header('Content-Type: application/json');

        print json_encode($data);
    }

    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function success($message)
    {
        return $this->make([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    public function fail($message)
    {
        return $this->make([
            'status' => 'fail',
            'message' => $message,
        ]);
    }
}