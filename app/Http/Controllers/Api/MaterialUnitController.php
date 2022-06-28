<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MaterialUnitResource;
use App\Models\MaterialUnit;
use Illuminate\Http\Request;

class MaterialUnitController extends Controller
{
    public function index()
    {
        return $this->success(MaterialUnitResource::collection(MaterialUnit::all()));
    }
}
