<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InternalRequestException;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function me()
    {
        $user = auth('api')->user();
        if ($user) {
            $permissions = $user->getAllPermissions();
        } else {
            throw new InternalRequestException('', '', 401);
        }

        $user->load('notifications', 'roles', 'subsectors');
        $userData = array_merge((new UserResource($user))->resolve(), ['permissions' => $permissions]);

        return $this->success($userData);
    }

    public function index(Request $request)
    {
        // 创建查询构造器
        $builder = User::query()->with('roles', 'subsectors');

        if ($name = $request->input('name', '')) {
            $builder->where('name', 'like', '%' . $name . '%');
        }
        if ($email = $request->input('email', '')) {
            $builder->where('email', 'like', '%' . $email . '%');
        }

        if ($request->has('role') && $request->input('role') != 'all') {
            $role = $request->input('role');
            $builder->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        }

        list($pageSize, $offset) = $this->getPaginationParams($request);

        $total = $builder->count();
        $items = $builder->offset($offset)->limit($pageSize)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item['role_id'] = $item->roles->pluck('id');
                $item['subsector_id'] = $item->subsectors->pluck('id');
                return $item;
            });
        return $this->success(compact('items', 'total'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'email', 'max:50', 'unique:users'],
            'name' => ['required', 'between:3,25', 'unique:users'],
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6|confirmed',
            'subsector_id' => 'required',
        ]);

        $user = new User($request->only(['name', 'email', 'entry_date', 'phone']));
        $user->fill([
            'password' => bcrypt($request->input('password')),
        ]);
        $user->save();

        $roleId = $request->input('role_id');
        $subsectorId = $request->input('subsector_id');
        $user->roles()->attach([$roleId]);
        $user->subsectors()->sync($subsectorId);

        return $this->success();
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'email' => ['required', 'email', 'max:50', 'unique:users,email,' . $user->id],
            'name' => ['required', 'between:3,25', 'unique:users,name,' . $user->id],
            'password' => 'min:6|confirmed',
            'role_id' => 'required',
            'subsector_id' => 'required',
        ]);

        $user->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'entry_date' => $request->input('entry_date'),
            'phone' => $request->input('phone'),
        ]);
        $user->save();

        $roleId = $request->input('role_id');
        $subsectorId = $request->input('subsector_id');
        $user->roles()->sync($roleId);
        $user->subsectors()->sync($subsectorId);

        return $this->success(new UserResource($user->refresh()->load('roles', 'subsectors')));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->success();
    }

    public function toggleStatus(Request $request)
    {
        $user = User::query()->findOrFail($request->id);
        $user->status = $request->status ? 1 : 0;
        $user->save();
        return $this->success(new UserResource($user));
    }

    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'password_old' => 'required',
            'password_new' => 'required|between:6,20|confirmed',
        ]);

        $user = auth('api')->user();
        $user->makeVisible('password');

        if (!Hash::check($request->input('password_old'), $user->password)) {
            throw new InternalRequestException('你输入的当前密码不正确！');
        }

        $user->password = Hash::make($request->input('password_new'));
        $user->save();

        return $this->success();
    }
}
