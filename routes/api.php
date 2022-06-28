<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

Route::namespace('Api')->group(function() {
    // 登录
    Route::post('authorizations', 'AuthorizationsController@store');

    // 登录后可以访问的接口
    Route::middleware(['accept.json', 'api.refresh'])->group(function() { // token 无痛刷新 （需要登录）
        Broadcast::routes(); // 广播路由验证
        // 登出
        Route::delete('authorization/logout', 'AuthorizationsController@destroy');

        Route::get('user', 'UsersController@me');
        Route::get('users', 'UsersController@index');
        Route::delete('users/{user}', 'UsersController@destroy');

        Route::post('user/toggle', 'UsersController@toggleStatus');
        Route::post('users', 'UsersController@store');
        Route::put('users/{user}', 'UsersController@update');

        Route::get('roles', 'RolesController@index');
        Route::post('role/toggle', 'RolesController@toggleStatus');
        Route::post('role/set-permission', 'RolesController@setPermissionToRole')->middleware('permission:user.role.edit');
        Route::get('role/{role}', 'RolesController@show');
        Route::get('permissions', 'PermissionsController@index');

        Route::resource('subsector', 'SubsectorController')->only(['index', 'store', 'update', 'destroy']);
        Route::resource('fee-item', 'FeeItemController')->only(['index', 'store', 'update', 'destroy']);
        Route::resource('payment-type', 'PaymentTypeController')->only(['index', 'store', 'update', 'destroy']);
        Route::resource('material-type', 'MaterialTypeController')->only(['index', 'store', 'update', 'destroy']);

        Route::get('material-unit', 'MaterialUnitController@index');

        Route::get('material', 'MaterialController@index');
        Route::post('material', 'MaterialController@save');
        Route::post('material/get-excel-data', 'MaterialController@getExcelData');
        Route::post('material/check-excel-data', 'MaterialController@checkExcelData');
        Route::post('material/save-excl-data', 'MaterialController@saveExcelData');
    });

    // 下载、导出相关请求处理
    Route::middleware('api.refresh')->group(function() {
        Route::get('download-file', 'Controller@download');

        Route::get('material/export/download-template', 'MaterialController@downloadTemplate');
    });
});
