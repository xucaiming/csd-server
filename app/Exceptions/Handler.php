<?php

namespace App\Exceptions;

use App\Handlers\ExceptionReport;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        InternalRequestException::class,
        AuthenticationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // 自定义异常处理
        if ($exception instanceof InternalRequestException) {
            return $exception->render($request);
        }

        // 全局异常处理
        if ($request->is('api/*')) {
            $reporter = ExceptionReport::make($exception);

            if ($reporter->shouldReturn()) {
                return $reporter->report();
            }

            if (env('APP_DEBUG')) {
                // 开发环境，则显示详细错误信息
                return parent::render($request, $exception);
            } else {
                //线上环境,未知错误，则显示500
                $statusCode = $exception->getCode();
                if ($statusCode == 0) {
                    $statusCode = 500;
                }
                // TODO 临时这样处理
                return $reporter->prodReport($statusCode);
            }
        }
        return parent::render($request, $exception);
    }
}
