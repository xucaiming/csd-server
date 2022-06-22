<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    public function store(AuthorizationRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return $this->failed('用户名或密码错误！', 422);
        }

        if (!auth('api')->user()->status) {
            return $this->failed('此账号还未启用，请联系管理员开启！', 422);
        }

        $responseData = $this->respondWithToken($token);
        $responseData['message'] = '登录成功';
        return $this->setStatusCode(201)->success($responseData);
    }

    protected function respondWithToken($token)
    {
        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    public function destroy()
    {
        auth('api')->logout();
        return $this->success();
    }
}
