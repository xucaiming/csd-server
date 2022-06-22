<?php

namespace App\Handlers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Throwable;

class ExceptionReport
{
    use ApiResponse;

    /**
     * @var Throwable
     */
    public $exception;
    /**
     * @var Request
     */
    public $request;

    /**
     * @var
     */
    protected $report;

    function __construct(Request $request, Throwable $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * @var array
     */
    //当抛出这些异常时，可以使用我们定义的错误信息与HTTP状态码
    //可以把常见异常放在这里
    public $doReport = [
        AuthenticationException::class => ['授权信息已过期，请重新登录！', 401],
        ModelNotFoundException::class => ['该模型未找到', 404],
        AuthorizationException::class => ['没有此操作权限', 403],
        ValidationException::class => [],
        UnauthorizedHttpException::class => ['未登录或登录状态失效', 401],
        TokenInvalidException::class => ['登录状态已过期，请重新登录', 400],
        NotFoundHttpException::class => ['没有找到该页面', 404],
        MethodNotAllowedHttpException::class => ['访问方式不正确', 405],
        QueryException::class => ['查询参数错误', 500],
        UnauthorizedException::class => ['你没有此操作的权限', 403],
        ThrottleRequestsException::class => ['操作太频繁，请一分钟后再操作！', 500],
    ];

    public function register($className, callable $callback){

        $this->doReport[$className] = $callback;
    }

    /**
     * @return bool
     */
    public function shouldReturn(){
        //只有请求包含是json或者ajax请求时才有效
//        if (! ($this->request->wantsJson() || $this->request->ajax())){
//            return false;
//        }
        foreach (array_keys($this->doReport) as $report){
            if ($this->exception instanceof $report){
                $this->report = $report;
                return true;
            }
        }

        return false;
    }

    public static function make(Throwable $e){

        return new static(\request(),$e);
    }

    /**
     * @return mixed
     */
    public function report(){
        // 验证错误时默认响应第一条信息
        if ($this->exception instanceof ValidationException){
            $error = Arr::first($this->exception->errors());
            return $this->failed(Arr::first($error),$this->exception->status);
        }

        $message = $this->doReport[$this->report];

        if ($this->request->expectsJson()) {
            return $this->failed($message[0],$message[1]);
        }

        // return view('errors.' . $message[1], ['message' => $message[0]]); // 可根据不同的错误信息响应不同的视图
        return view('errors.error', [
            'message' => $message[0],
            'statusCode' => $message[1],
        ]);
    }

    public function prodReport($code = '500'){
        return $this->failed('服务器内部错误', $code);
    }
}
