<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Notifications\NewTicketReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with('latestReply')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('frontend.pages.member.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('frontend.pages.member.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'    => 'required|string|max:255',
            'priority'   => 'required|in:low,normal,high,urgent',
            'department' => 'nullable|string|max:100',
            'message'    => 'required|string|min:10',
        ]);

        $ticket = SupportTicket::create([
            'user_id'    => Auth::id(),
            'subject'    => x_clean($request->subject),
            'priority'   => $request->priority,
            'department' => $request->department,
            'status'     => SupportTicket::STATUS_NEW,
        ]);

        TicketReply::create([
            'ticket_id'     => $ticket->id,
            'user_id'       => Auth::id(),
            'message'       => x_clean($request->message),
            'is_staff_reply' => false,
        ]);

        return redirect()->route('member.tickets.show', $ticket->ticket_number)
            ->with('success', __tr('Support ticket created. We will respond shortly.'));
    }

    public function show(string $ticketNumber)
    {
        $ticket = SupportTicket::with(['replies.user', 'assignedAdmin'])
            ->where('ticket_number', $ticketNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('frontend.pages.member.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, string $ticketNumber)
    {
        $request->validate(['message' => 'required|string|min:5']);

        $ticket = SupportTicket::where('ticket_number', $ticketNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($ticket->status === SupportTicket::STATUS_CLOSED) {
            $ticket->update(['status' => SupportTicket::STATUS_RE_OPEN]);
        }

        TicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => Auth::id(),
            'message'        => x_clean($request->message),
            'is_staff_reply' => false,
        ]);

        return back()->with('success', __tr('Reply sent.'));
    }

    public function close(string $ticketNumber)
    {
        SupportTicket::where('ticket_number', $ticketNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail()
            ->update([
                'status'    => SupportTicket::STATUS_CLOSED,
                'closed_at' => now(),
            ]);

        return back()->with('success', __tr('Ticket closed.'));
    }
}
