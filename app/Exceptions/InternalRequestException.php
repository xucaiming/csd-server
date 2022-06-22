<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InternalRequestException extends Exception
{
    protected $msgForUser;
    protected $code;

    public function __construct(string $message, string $msgForUser = '服务器内部异常', int $code = 500)
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
        $this->code = $code;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'code' => $this->code,
                'message' => $this->message,
                'status' => 'error',
            ], $this->code);
        }
    }

}
