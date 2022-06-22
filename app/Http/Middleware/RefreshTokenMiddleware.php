<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class RefreshTokenMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 将响应头 Authorization 暴露给前端
        header('Access-Control-Expose-Headers: Authorization');

        // 检查此次请求中是否带有token，如果没有则抛出异常
        $this->checkForToken($request);

        try {
            // 检查用户的登录状态，如果正常则通过
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }
            throw new UnauthorizedHttpException('jwt-auth', '未登录');

        } catch (TokenExpiredException $exception) {

            // 此处如果捕获到了 token 过期说抛出的 TokenExpiredException 异常
            // 则要刷新该用户的token 并将 它添加到响应头中
            try {

                // 使用一次性登录以保证此次请求成功
                auth('api')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);

                if ($request->expectsJson()){

                    // 刷新用户的 token
                    $token = $this->auth->refresh();
                    // 在响应头中返回新的 token
                    return $this->setAuthenticationHeader($next($request), $token);
                }

                return $next($request);

                // auth('api')->setToken($request->token)->invalidate();

            } catch (JWTException $exception) {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录
                throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
            }
        }
    }
}
