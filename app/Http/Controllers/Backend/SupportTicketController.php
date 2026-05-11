<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\NewTicketReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'latestReply', 'assignedAdmin'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->q . '%')
                  ->orWhere('subject', 'like', '%' . $request->q . '%');
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        $stats = [
            'new'         => SupportTicket::where('status', SupportTicket::STATUS_NEW)->count(),
            'in_progress' => SupportTicket::where('status', SupportTicket::STATUS_IN_PROGRESS)->count(),
            'closed'      => SupportTicket::where('status', SupportTicket::STATUS_CLOSED)->count(),
        ];

        $admins = User::where('type', config('settings.user_type.admin', 1))->get();

        return view('backend.modules.support-tickets.index', compact('tickets', 'stats', 'admins'));
    }

    public function show(int $id)
    {
        $ticket = SupportTicket::with(['replies.user', 'user', 'assignedAdmin'])->findOrFail($id);
        $admins = User::where('type', config('settings.user_type.admin', 1))->get();

        return view('backend.modules.support-tickets.show', compact('ticket', 'admins'));
    }

    public function reply(Request $request, int $id)
    {
        $request->validate(['message' => 'required|string|min:5']);

        $ticket = SupportTicket::findOrFail($id);

        $reply = TicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => Auth::id(),
            'message'        => x_clean($request->message),
            'is_staff_reply' => true,
        ]);

        if (!$ticket->first_reply_at) {
            $ticket->update(['first_reply_at' => now()]);
        }

        if ($ticket->status === SupportTicket::STATUS_NEW) {
            $ticket->update(['status' => SupportTicket::STATUS_IN_PROGRESS]);
        }

        // Notify the customer
        $ticket->user->notify(new NewTicketReplyNotification($ticket, $reply));

        toastNotification('success', __tr('Reply sent.'));
        return back();
    }

    public function assign(Request $request)
    {
        $request->validate([
            'ticket_id'   => 'required|integer|exists:support_tickets,id',
            'assigned_to' => 'required|integer|exists:users,id',
        ]);

        SupportTicket::findOrFail($request->ticket_id)
            ->update(['assigned_to' => $request->assigned_to]);

        toastNotification('success', __tr('Ticket assigned.'));
        return back();
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:support_tickets,id',
            'status'    => 'required|integer|in:1,2,3,4',
        ]);

        $data = ['status' => $request->status];

        if ((int) $request->status === SupportTicket::STATUS_CLOSED) {
            $data['closed_at'] = now();
        }

        SupportTicket::findOrFail($request->ticket_id)->update($data);

        toastNotification('success', __tr('Ticket status updated.'));
        return back();
    }
}
