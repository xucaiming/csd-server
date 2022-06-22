<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $builder = Role::query()->with('permissions');
        if ($status = $request->input('status')) {
            $builder->where('status', $status);
        }

        if($request->has('page') && $request->has('pageSize')) {
            $page = $request->page;
            $pageSize = $request->pageSize;
            $offset = ($page - 1) * $pageSize;
            $total = $builder->count();

            $roles = $builder->offset($offset)->limit($pageSize)->get();
            $items = RoleResource::collection($roles);
            return $this->success(compact('items', 'total'));
        }
        return $this->success(RoleResource::collection(Role::all()));
    }

    public function toggleStatus(Request $request)
    {
        $role = Role::query()->findOrFail($request->id);
        $role->status = $request->status ? 1 : 0;
        $role->save();
        return $this->success(new RoleResource($role));
    }

    public function setPermissionToRole(Request $request)
    {
        $this->validate($request, [
            'desc' => 'required',
            'menu' => 'array',
        ]);
        $role = Role::query()->findOrFail($request->id);
        $role->desc = $request->desc;
        $role->save();

        $menu = $request->menu['checked'] ?? $request->menu;

        // 保存角色
        $role->permissions()->sync($menu, true);
        return $this->success(new RoleResource($role));
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return $this->success(new RoleResource($role));
    }
}
