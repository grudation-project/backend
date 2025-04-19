<?php

namespace Modules\Ticket\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Ticket\Http\Requests\TicketFilterRequest;
use Modules\Ticket\Http\Requests\UserTicketRequest;
use Modules\Ticket\Services\UserTicketService;
use Modules\Ticket\Transformers\TicketResource;

class UserTicketController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly UserTicketService $userTicketService)
    {
    }

    public function index(TicketFilterRequest $request)
    {
        $tickets = $this->userTicketService->index($request->validated());

        return $this->paginatedResponse($tickets, TicketResource::class);
    }

    public function show($id)
    {
        $ticket = $this->userTicketService->show($id);

        return $this->resourceResponse(TicketResource::make($ticket));
    }

    public function store(UserTicketRequest $request)
    {
        $ticket = $this->userTicketService->store($request->validated());

        return $this->createdResponse(TicketResource::make($ticket), translate_success_message('ticket', 'created_female'));
    }

    public function update(UserTicketRequest $request, $id)
    {
        $ticket = $this->userTicketService->update($request->validated(), $id);

        return $this->okResponse(TicketResource::make($ticket), translate_success_message('ticket', 'updated_female'));
    }
}
