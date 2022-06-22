<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SubsectorRequest;
use App\Http\Resources\SubsectorResource;
use App\Models\Subsector;
use Illuminate\Http\Request;

class SubsectorController extends Controller
{
    public function index(Request $request)
    {
        $builder = Subsector::query()
            ->withCount('users')
            ->orderBy('id', 'desc');

        if ($status = $request->input('status', 1)) {
            $builder->where('status', $status);
        }

        $subsectors = $builder->get();
        return $this->success(SubsectorResource::collection($subsectors));
    }

    public function store(SubsectorRequest $request)
    {
        $saveData = $request->all();

        $subsector = new Subsector($saveData);
        $subsector->save();

        return $this->success(new SubsectorResource($subsector->refresh()->loadCount('users')));
    }

    public function update(SubsectorRequest $request, Subsector $subsector)
    {
        $saveData = $request->all();

        $subsector->fill($saveData);
        $subsector->save();

        return $this->success(new SubsectorResource($subsector->refresh()->loadCount('users')));
    }

    public function destroy(Subsector $subsector)
    {
        $subsector->delete();
        return $this->success();
    }
}
