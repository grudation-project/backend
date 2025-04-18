<?php

namespace Modules\Technician\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Technician\Http\Requests\TechnicianRequest;
use Modules\Technician\Services\TechnicianService;
use Modules\Technician\Transformers\TechnicianResource;

class ManagerTechnicianController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly TechnicianService $technicianService)
    {
    }

    public function index()
    {
        $items = $this->technicianService->index();

        return $this->resourceResponse(TechnicianResource::collection($items));
    }

    public function show($id)
    {
        $item = $this->technicianService->show($id);

        return $this->resourceResponse(TechnicianResource::make($item));
    }

    public function store(TechnicianRequest $request)
    {
        $user = $this->technicianService->store($request->validated());

        return $this->createdResponse(
            TechnicianResource::make($user),
            translate_success_message('technician', 'created')
        );
    }

    public function update(TechnicianRequest $request, $id)
    {
        $item = $this->technicianService->update($request->validated(), $id);

        return $this->okResponse(
            TechnicianResource::make($item),
            translate_success_message('technician', 'updated')
        );
    }

    public function destroy($id)
    {
        $this->technicianService->destroy($id);

        return $this->okResponse(message: translate_success_message('technician', 'deleted'));
    }
}
