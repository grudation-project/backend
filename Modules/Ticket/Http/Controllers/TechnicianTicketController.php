<?php

namespace Modules\Ticket\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Technician\Exceptions\TechnicianException;
use Modules\Ticket\Http\Requests\TicketFilterRequest;
use Modules\Ticket\Services\TechnicianTicketService;
use Modules\Ticket\Transformers\TicketResource;

class TechnicianTicketController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly TechnicianTicketService $technicianTicketService)
    {
    }

    public function index(TicketFilterRequest $request)
    {
        $tickets = $this->technicianTicketService->index($request->validated());

        return $this->paginatedResponse($tickets, TicketResource::class);
    }

    public function show($id)
    {
        $ticket = $this->technicianTicketService->show($id);

        return $this->resourceResponse(TicketResource::make($ticket));
    }

    /**
     * @throws TechnicianException
     */
    public function resolve($id)
    {
        $this->technicianTicketService->resolve($id);

        return $this->okResponse(message: translate_word('ticket_resolved'));
    }
}
