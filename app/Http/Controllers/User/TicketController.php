<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\ConvertsDates;
use Illuminate\Http\Request;
use Coderflex\LaravelTicket\Models\Ticket;
use Coderflex\LaravelTicket\Models\Message;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
	use ConvertsDates;

    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
                        ->with(['messages', 'user'])
                        ->latest()
                        ->paginate(10);
						
		$tickets->transform(function ($ticket) {
            $ticket->created_at = $this->convertToUserTimezone($ticket->created_at);
            $ticket->updated_at = $this->convertToUserTimezone($ticket->updated_at);
            return $ticket;
        });
                        
        return view('theme::pages.user.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('theme::pages.user.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high'
        ]);

        $ticket = Ticket::create([
            'title' => $request->title,
            'user_id' => Auth::id(),
            'status' => 'open',
            'priority' => $request->priority
        ]);

        Message::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        return redirect()->route('user.tickets.index')
                        ->with('success', __('The ticket was successfully created'));
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load(['messages.user', 'user']);
		
		$ticket->created_at = $this->convertToUserTimezone($ticket->created_at);
        $ticket->updated_at = $this->convertToUserTimezone($ticket->updated_at);
        
        return view('theme::pages.user.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string'
        ]);

        Message::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        return redirect()->back()->with('success', __('The ticket was successfully replyed.'));
    }
}