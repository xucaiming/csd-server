<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BaseService
{
    public function uploadFile($file, $folder, User $user)
    {
        $folder_name = 'uploads/'. $folder .'/'. date("Ym/d", time());
        $upload_path = public_path() . '/' . $folder_name;
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $user->id . '-' . Str::random(15) . '.' . $extension;
        if ($file->move($upload_path, $filename)) {
            return [
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $folder_name . '/' . $filename,
            ];
        }
        return false;
    }

    protected function getPaginationParams(Request $request)
    {
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 15);
        $offset = ($page - 1) * $pageSize;

        return [$pageSize, $offset];
    }
}
