<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Service\Models\Service;
use Modules\Service\Transformers\ServiceResource;

class SelectMenuController extends Controller
{
   use HttpResponse;

   public function services()
   {
       $ignoredId = request()->input('ignored_id');
       $onlyUnique = request()->input('only_unique', false);
       $services = Service::query()
           ->latest()
           ->when($onlyUnique, fn($q) => $q->onlyUnique($ignoredId))
           ->get(['id', 'name']);

       return $this->resourceResponse(ServiceResource::collection($services));
   }
}
