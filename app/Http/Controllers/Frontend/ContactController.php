<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactUsMessage;
use App\Models\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function contactPage(): View
    {
        $keys = [
            'contact_title',
            'contact_sub_title',
            'contact_address',
            'contact_email',
            'contact_phone_1',
            'contact_phone_2',
            'contact_opening_hours',
            'contact_closed_hours',
        ];

        $content = PageContent::whereIn('key', $keys)
            ->get()
            ->pluck('value', 'key');

        return view('frontend.pages.contact', compact('content'));
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:3000',
        ]);

        ContactUsMessage::create([
            'name'    => xss_clean($request->name),
            'email'   => $request->email,
            'subject' => xss_clean($request->subject),
            'message' => xss_clean($request->message),
        ]);

        return redirect()->back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
    }
}
