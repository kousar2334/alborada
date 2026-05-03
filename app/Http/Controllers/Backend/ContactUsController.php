<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\ContactUsMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactUsController extends Controller
{
    /**
     * Show paginated contact messages list.
     */
    public function messages(Request $request): View
    {
        $query = ContactUsMessage::orderBy('id', 'DESC');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'replied') {
                $query->whereNotNull('replied_at');
            }
        }

        $messages = $query->paginate(15)->withQueryString();

        return view('backend.modules.messages.messages', compact('messages'));
    }

    /**
     * Mark a message as read and return message data for the view modal.
     */
    public function viewMessage(Request $request): RedirectResponse
    {
        $message = ContactUsMessage::findOrFail($request->id);

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return redirect()->route('admin.contact.us.message.list', ['view' => $message->id])
            ->withInput();
    }

    /**
     * Send a reply email to the contact message sender.
     */
    public function replyMessage(Request $request): RedirectResponse
    {
        $request->validate([
            'id'            => 'required|exists:contact_us_messages,id',
            'reply_message' => 'required|string|max:3000',
        ]);

        try {
            $message = ContactUsMessage::findOrFail($request->id);

            Mail::to($message->email)->send(new ContactReplyMail(
                replyMessage: $request->reply_message,
                originalSubject: $message->subject,
                senderName: $message->name,
            ));

            $message->update([
                'is_read'       => true,
                'reply_message' => $request->reply_message,
                'replied_at'    => now(),
            ]);

            toastNotification('success', 'Reply sent successfully to ' . $message->email, 'Success');
        } catch (\Exception) {
            toastNotification('error', 'Failed to send reply. Please check your SMTP settings.', 'Error');
        }

        return to_route('admin.contact.us.message.list');
    }

    /**
     * Delete a contact message.
     */
    public function deleteMessage(Request $request): RedirectResponse
    {
        try {
            $message = ContactUsMessage::findOrFail($request->id);
            $message->delete();
            toastNotification('success', 'Message deleted successfully', 'Success');
        } catch (\Exception $e) {
            toastNotification('error', 'Message delete failed', 'Error');
        }

        return to_route('admin.contact.us.message.list');
    }
}
