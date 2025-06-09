<?php

namespace Modules\Manager\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Manager\Http\Requests\ManagerSettingRequest;
use Modules\Manager\Services\ManagerSettingService;
use Modules\Manager\Transformers\ManagerSettingResource;

class ManagerSettingController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ManagerSettingService $managerSettingService) {}

    public function show()
    {
        $setting = $this->managerSettingService->show();

        return $this->resourceResponse($setting);
    }

    public function update(ManagerSettingRequest $request)
    {
        $setting = $this->managerSettingService->update($request->validated());

        return $this->okResponse($setting, message: translate_success_message('manager_setting', 'updated'));
    }
}
