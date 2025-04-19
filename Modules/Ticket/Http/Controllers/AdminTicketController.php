<?php

namespace Modules\Ticket\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Ticket\Http\Requests\TicketFilterRequest;
use Modules\Ticket\Services\AdminTicketService;
use Modules\Ticket\Transformers\TicketResource;

class AdminTicketController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly AdminTicketService $adminTicketService)
    {
    }

    public function index(TicketFilterRequest $request)
    {
        $tickets = $this->adminTicketService->index($request->validated());

        return $this->paginatedResponse($tickets, TicketResource::class);
    }

    public function show($id)
    {
        $ticket = $this->adminTicketService->show($id);

        return $this->resourceResponse(TicketResource::make($ticket));
    }
}
