<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::orderBy('sort_order')->orderByDesc('created_at')->get();
        return view('backend.modules.channels.index', compact('channels'));
    }

    public function create()
    {
        return view('backend.modules.channels.form', ['channel' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validateChannel($request);
        Channel::create($data);
        toastNotification('success', __tr('Channel added successfully.'));
        return redirect()->route('admin.channels.index');
    }

    public function edit(Channel $channel)
    {
        return view('backend.modules.channels.form', compact('channel'));
    }

    public function update(Request $request, Channel $channel)
    {
        $data = $this->validateChannel($request, $channel);

        if (empty($data['logo'])) {
            unset($data['logo']);
        }

        $channel->update($data);
        toastNotification('success', __tr('Channel updated successfully.'));
        return redirect()->route('admin.channels.index');
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();
        toastNotification('success', __tr('Channel deleted.'));
        return redirect()->route('admin.channels.index');
    }

    public function toggleStatus(Channel $channel)
    {
        $channel->update(['status' => !$channel->status]);
        return response()->json(['status' => $channel->status]);
    }

    private function validateChannel(Request $request, ?Channel $channel = null): array
    {
        return $request->validate([
            'name'       => 'required|string|max:150',
            'logo'       => 'nullable|string|max:500',
            'bg_color'   => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'status'     => 'nullable|boolean',
        ]) + ['status' => $request->boolean('status', true)];
    }
}
