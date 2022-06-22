<?php

namespace App\Handlers;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Response;

trait ApiResponse
{

    protected $statusCode = FoundationResponse::HTTP_OK;

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode, $httpCode = null)
    {
        // todo
        $httpCode = $httpCode ?? $statusCode;
//        $this->statusCode = $statusCode;
        $this->statusCode = $httpCode;
        return $this;
    }

    public function respond($data, $header = [])
    {
        return Response::json($data, $this->getStatusCode(), $header);
    }

    public function status($status, array $data, $code = null)
    {
        if ($code) {
            $this->setStatusCode($code);
        }

        $status = [
            'status' => $status,
            'code' => $this->statusCode
        ];

        $data = array_merge($status, $data);
        return $this->respond($data);
    }

    /*
     * 格式
     * data:
     *  code:422
     *  message:xxx
     *  status:'error'
     */
    public function failed($message = 'Internal Server exception', $code = FoundationResponse::HTTP_INTERNAL_SERVER_ERROR, $status = 'error'){

        return $this->setStatusCode($code)->message($message,$status);
    }

    public function message($message, $status = "success"){
        return $this->status($status,[
            'message' => $message
        ]);
    }

    public function internalError($message = "Internal Error!"){

        return $this->failed($message,FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function created($message = "created")
    {
        return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
            ->message($message);
    }

    public function success($data = [], $status = "success"){

        return $this->status($status,compact('data'));
    }

    public function notFond($message = 'Not Fond!')
    {
        return $this->failed($message,Foundationresponse::HTTP_NOT_FOUND);
    }
}
