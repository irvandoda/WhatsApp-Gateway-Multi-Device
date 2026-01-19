<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ConvertsDates;
use Illuminate\Http\Request;
use Coderflex\LaravelTicket\Models\Ticket;
use Coderflex\LaravelTicket\Models\Message;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
	use ConvertsDates;
	
    public function index(Request $request)
    {
		if($request->user()->level != 'admin'){
			return redirect()->route('home');
		}
		
        $tickets = Ticket::with(['messages', 'user'])
                        ->latest()
                        ->paginate(10);
						
		$tickets->transform(function ($ticket) {
            $ticket->created_at = $this->convertToUserTimezone($ticket->created_at);
            $ticket->updated_at = $this->convertToUserTimezone($ticket->updated_at);
            return $ticket;
        });
                        
        return view('theme::pages.admin.tickets.index', compact('tickets'));
    }

    public function show(Request $request, Ticket $ticket)
    {
		if($request->user()->level != 'admin'){
			return redirect()->route('home');
		}
		
        $ticket->load(['messages.user', 'user']);
		
		$ticket->created_at = $this->convertToUserTimezone($ticket->created_at);
        $ticket->updated_at = $this->convertToUserTimezone($ticket->updated_at);
        
        return view('theme::pages.admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
		if($request->user()->level != 'admin'){
			return redirect()->route('home');
		}
		
        $request->validate([
            'message' => 'required|string'
        ]);

        Message::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        return redirect()->back()->with('success', __('The ticket has been answered.'));
    }

    public function close(Request $request, Ticket $ticket)
    {
		if($request->user()->level != 'admin'){
			return redirect()->route('home');
		}
		
        $ticket->update(['status' => 'closed']);
        
        return redirect()->back()->with('success', __('The ticket is closed.'));
    }

    public function reopen(Request $request, Ticket $ticket)
    {
		if($request->user()->level != 'admin'){
			return redirect()->route('home');
		}
		
        $ticket->update(['status' => 'open']);
        
        return redirect()->back()->with('success', __('The ticket has been reopened successfully.'));
    }
}