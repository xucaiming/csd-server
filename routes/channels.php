<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// notification 频道
Broadcast::channel('App.Models.User.{id}', function (\App\Models\User $user, $id) {
    return (int) $user->id === (int) $id;
});

//Broadcast::channel('inbound-order.{order}', function (\App\Models\User $user, \App\Models\InboundOrder $order) {
//    if ($user->hasRole(\App\Models\Model::Customer)) {
//        return $order->user_id === $user->id;
//    }
//
//    if ($user->hasRole(\App\Models\Model::Servicer)) {
//        return $user->users->pluck('id')->has($order->user_id);
//    }
//    return false;
//});
