<?php

namespace Modules\Service\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Service\Http\Requests\ServiceRequest;
use Modules\Service\Services\AdminServiceLogic;
use Modules\Service\Transformers\ServiceResource;

class AdminServiceController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly AdminServiceLogic $adminServiceLogic)
    {

    }

    public function index()
    {
        $services = $this->adminServiceLogic->index();

        return $this->resourceResponse(ServiceResource::collection($services));
    }

    public function show($id)
    {
        $service = $this->adminServiceLogic->show($id);

        return $this->resourceResponse(ServiceResource::make($service));
    }

    public function store(ServiceRequest $request)
    {
        $service = $this->adminServiceLogic->store($request->validated());

        return $this->createdResponse(ServiceResource::make($service), translate_success_message('service', 'created'));
    }

    public function update(ServiceRequest $request, $id)
    {
        $service = $this->adminServiceLogic->update($request->validated(), $id);

        return $this->okResponse(ServiceResource::make($service), translate_success_message('service', 'updated'));
    }

    public function destroy($id)
    {
        $this->adminServiceLogic->destroy($id);

        return $this->okResponse(message: translate_success_message('service', 'deleted'));
    }
}
