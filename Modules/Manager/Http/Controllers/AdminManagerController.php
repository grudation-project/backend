<?php

namespace Modules\Manager\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Manager\Http\Requests\ManagerRequest;
use Modules\Manager\Services\AdminManagerService;
use Modules\Manager\Transformers\ManagerResource;

class AdminManagerController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly AdminManagerService $adminManagerService)
    {
    }

    public function index()
    {
        $managers = $this->adminManagerService->index();

        return $this->paginatedResponse($managers, ManagerResource::class);
    }

    public function show($id)
    {
        $manager = $this->adminManagerService->show($id);

        return $this->resourceResponse(ManagerResource::make($manager));
    }

    public function store(ManagerRequest $request)
    {
        $manager = $this->adminManagerService->store($request->validated());

        return $this->createdResponse(ManagerResource::make($manager));
    }

    public function update(ManagerRequest $request, $id)
    {
        $manager = $this->adminManagerService->update($request->validated(), $id);

        return $this->okResponse(ManagerResource::make($manager), translate_success_message('manager', 'updated'));
    }

    public function destroy($id)
    {
        $this->adminManagerService->destroy($id);

        return $this->okResponse(message: translate_success_message('manager', 'deleted'));
    }
}
