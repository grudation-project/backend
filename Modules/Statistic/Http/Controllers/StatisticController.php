<?php

namespace Modules\Statistic\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Modules\Statistic\Services\ManagerStatisticService;
use Modules\Statistic\Services\StatisticService;
use Modules\Statistic\Services\TechnicianStatisticService;
use Modules\Statistic\Services\UserStatisticService;

class StatisticController extends Controller
{
   use HttpResponse;

   public function admin(StatisticService $statisticService)
   {
        return $this->resourceResponse($statisticService->handle());
   }

   public function user(UserStatisticService $userStatisticService) {
        return $this->resourceResponse($userStatisticService->handle());
   }

   public function manager(ManagerStatisticService $managerStatisticService) {
        return $this->resourceResponse($managerStatisticService->handle());
   }

   public function technician(TechnicianStatisticService $technicianStatisticService) {
        return $this->resourceResponse($technicianStatisticService->handle());
   }
}
