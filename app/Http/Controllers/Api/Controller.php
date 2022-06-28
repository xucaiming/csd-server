<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ApiResponse;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use ApiResponse;

    // 获取分页参数
    protected function getPaginationParams(Request $request)
    {
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 15);
        $offset = ($page - 1) * $pageSize;

        return [$pageSize, $offset];
    }

    public function download(Request $request)
    {
        $model_name = $request->input('model_name');
        $file_id = $request->input('file_id');
        $fileModel = app('App\\Models\\' . $model_name)->query()->findOrFail($file_id);

        return response()->download($fileModel->file_path, $fileModel->original_name);
    }
}
