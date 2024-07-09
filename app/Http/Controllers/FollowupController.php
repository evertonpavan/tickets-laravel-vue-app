<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FollowupController extends Controller
{
    public function index(Ticket $ticket)
    {
        $followups = $ticket->followups()->get();
        return Inertia::render('Followups/Index', [
            'followups' => $followups,
            'ticket' => $ticket,
        ]);
    }

    public function show(Followup $followup)
    {
        return Inertia::render('Followups/Show', [
            'followup' => $followup,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'type' => 'required|in:comment,solution',
            'content' => 'required|string',
        ]);

        $followup = Followup::create([
            'ticket_id' => $request->ticket_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('message', 'Followup created successfully.');
    }

    public function update(Request $request, Followup $followup)
    {
        Gate::authorize('update', Followup::class);

        $request->validate([
            'content' => 'required|string',
        ]);

        $followup->update([
            'content' => $request->content,
        ]);

        return redirect()->back()->with('message', 'Followup updated successfully.');
    }

    public function destroy(Followup $followup)
    {
        Gate::authorize('delete' ,Followup::class);

        $followup->delete();

        return redirect()->back()->with('message', 'Followup deleted successfully.');
    }
}
