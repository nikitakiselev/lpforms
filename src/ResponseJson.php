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

        echo json_encode($data);
    }

    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function success($message)
    {
        $this->make([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    public function fail($message)
    {
        $this->make([
            'status' => 'fail',
            'message' => $message,
        ]);
    }
}
