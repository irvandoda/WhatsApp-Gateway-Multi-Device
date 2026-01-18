<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class TicketController extends Controller
{
    public function index()
	{
		$tickets = auth()->user()->tickets()->latest()->get();
		
		$tickets->transform(function ($ticket) {
            $ticket->created_at = Carbon::parse($ticket->created_at)->timezone(auth()->user()->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s');
            $ticket->updated_at = Carbon::parse($ticket->updated_at)->timezone(auth()->user()->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s');
            return $ticket;
        });
		
		return view('theme::tickets.index', compact('tickets'));
	}
	
	public function create()
	{
		return view('theme::tickets.create');
	}

	public function store(Request $request)
	{
		$request->validate([
			'title' => 'required|string|max:255',
			'message' => 'required|string',
		]);

		auth()->user()->tickets()->create([
			'title' => $request->title,
			'message' => $request->message,
		]);

		return redirect()->route('tickets.index')->with('success', __('The ticket has been created.'));
	}
	
	public function adminIndex()
	{
		$tickets = \Coderflex\LaravelTicket\Models\Ticket::latest()->get();
		
		$tickets->transform(function ($ticket) {
            $ticket->created_at = Carbon::parse($ticket->created_at)->timezone(auth()->user()->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s');
            $ticket->updated_at = Carbon::parse($ticket->updated_at)->timezone(auth()->user()->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s');
            return $ticket;
        });
		
		return view('theme::pages.admin.tickets.index', compact('tickets'));
	}

	public function show($id)
	{
		$ticket = \Coderflex\LaravelTicket\Models\Ticket::findOrFail($id);
		
		$tickets->transform(function ($ticket) {
            $ticket->created_at = Carbon::parse($ticket->created_at)->timezone(auth()->user()->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s');
            $ticket->updated_at = Carbon::parse($ticket->updated_at)->timezone(auth()->user()->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s');
            return $ticket;
        });
		
		return view('theme::pages.admin.tickets.show', compact('ticket'));
	}
	
	public function reply(Request $request, $id)
	{
		$request->validate([
			'reply' => 'required|string',
		]);

		$ticket = \Coderflex\LaravelTicket\Models\Ticket::findOrFail($id);
		$ticket->replies()->create([
			'message' => $request->reply,
			'user_id' => auth()->id(),
		]);

		return redirect()->route('admin.tickets.show', $id)->with('success', __('The ticket has been answered.'));
	}
	
	public function close($id)
	{
		$ticket = \Coderflex\LaravelTicket\Models\Ticket::findOrFail($id);
		$ticket->update(['status' => 'closed']);

		return redirect()->route('admin.tickets.index')->with('success', __('The ticket is closed.'));
	}
}
