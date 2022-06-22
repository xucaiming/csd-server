<?php

namespace App\Handlers\OrderPlatforms;

use App\Models\StoreAccount;

interface OrderPlatform {

    public function syncOrder(StoreAccount $account, array $options);

}
