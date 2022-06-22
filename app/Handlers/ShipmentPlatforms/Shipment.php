<?php

namespace App\Handlers\ShipmentPlatforms;

class Shipment
{
    protected $fromAddress = [];
    protected $toAddress = [];
    protected $parcel = [];

    protected $selectedRate = [];

    protected $accountInfo = [];

    public function setFromAddress($address)
    {
        $this->fromAddress = $address;
        return $this;
    }

    public function setToAddress($address)
    {
        $this->toAddress = $address;
        return $this;
    }

    public function setParcel($parcel)
    {
        $this->parcel = $parcel;
        return $this;
    }

    public function setSelectedRate($selectedRate)
    {
        $this->selectedRate = $selectedRate;
        return $this;
    }

    public function succeed($data)
    {
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    public function failed($message)
    {
        return [
            'success' => false,
            'message' => $message,
        ];
    }
}
