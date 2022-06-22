<?php

namespace App\Http\Controllers\Api;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return $this->success(PermissionResource::collection($permissions));
    }
}
