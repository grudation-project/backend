<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Manager\Helpers\ManagerHelper;
use Modules\Service\Models\Section;
use Modules\Service\Models\Service;
use Modules\Service\Transformers\SectionResource;
use Modules\Service\Transformers\ServiceResource;
use Modules\Technician\Models\Technician;
use Modules\Technician\Transformers\TechnicianResource;

class SelectMenuController extends Controller
{
    use HttpResponse;

    public function services()
    {
        $ignoredId = request()->input('ignored_id');
        $onlyUnique = request()->input('only_unique', false);
        $onlyAssociatedToManagers = request()->input('only_associated_to_managers', false);

        $services = Service::query()
            ->latest()
            ->when($onlyUnique, fn($q) => $q->onlyUnique($ignoredId))
            ->when($onlyAssociatedToManagers, fn($q) => $q->whereHas('manager'))
            ->get(['id', 'name']);

        return $this->resourceResponse(ServiceResource::collection($services));
    }

    public function sections()
    {
        $serviceId = request()->input('service_id');
        $sections = Section::query()->latest()->when($serviceId, fn($q) => $q->where('service_id', $serviceId))->get();

        return $this->resourceResponse(SectionResource::collection($sections));
    }

    public function technicians()
    {
        $technicians = Technician::query()
            ->latest()
            ->with('user:id,name')
            ->where('manager_id', ManagerHelper::getUserManager()->id)
            ->get();

        return $this->resourceResponse(TechnicianResource::collection($technicians));
    }
}
