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

       $services = Service::query()
           ->latest()
           ->whereDoesntHave('manager', function($q) use ($ignoredId){
              $q->when(!is_null($ignoredId), fn($q2) => $q2->where('id', '<>', $ignoredId));
           })
           ->get(['id', 'name']);

       return $this->resourceResponse(ServiceResource::collection($services));
   }
}
