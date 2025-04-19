<?php

namespace Modules\Ticket\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Ticket\Http\Requests\TicketAssignRequest;
use Modules\Ticket\Http\Requests\TicketFilterRequest;
use Modules\Ticket\Services\ManagerTicketService;
use Modules\Ticket\Transformers\TicketResource;

class ManagerTicketController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ManagerTicketService $managerTicketService)
    {
    }

    public function index(TicketFilterRequest $request)
    {
        $tickets = $this->managerTicketService->index($request->validated());

        return $this->paginatedResponse($tickets, TicketResource::class);
    }

    public function show($id)
    {
        $ticket = $this->managerTicketService->show($id);

        return $this->resourceResponse(TicketResource::make($ticket));
    }

    public function assign(TicketAssignRequest $request, $id)
    {
        $this->managerTicketService->assign($request->validated(), $id);

        return $this->okResponse(message: translate_word('ticket_assigned'));
    }

    public function resolve($id)
    {
        $this->managerTicketService->resolve($id);

        return $this->okResponse(message: translate_word('ticket_resolved'));
    }
}
