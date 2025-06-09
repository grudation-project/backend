<?php

namespace Modules\Service\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Service\Http\Requests\SectionRequest;
use Modules\Service\Http\Requests\ServiceRequest;
use Modules\Service\Services\AdminSectionService;
use Modules\Service\Transformers\SectionResource;

class AdminSectionController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly AdminSectionService $adminSectionService) {}

    public function index($serviceId)
    {
        $sections = $this->adminSectionService->index($serviceId);

        return $this->resourceResponse(SectionResource::collection($sections));
    }

    public function show($serviceId, $id)
    {
        $section = $this->adminSectionService->show($serviceId, $id);

        return $this->resourceResponse(SectionResource::make($section));
    }

    public function store(ServiceRequest $request, $serviceId)
    {
        $section = $this->adminSectionService->store($request->validated(), $serviceId);

        return $this->createdResponse(SectionResource::make($section), translate_success_message('section', 'created'));
    }

    public function update(ServiceRequest $request, $serviceId, $id)
    {
        $section = $this->adminSectionService->update($request->validated(), $serviceId, $id);

        return $this->okResponse(SectionResource::make($section), translate_success_message('section', 'updated'));
    }

    public function destroy($serviceId, $id)
    {
        $this->adminSectionService->destroy($serviceId, $id);

        return $this->okResponse(message: translate_success_message('section', 'deleted'));
    }
}
