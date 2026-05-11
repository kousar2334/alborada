<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ApiLog::with('user:id,name,email')
            ->latest('created_at');

        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->user . '%')
                ->orWhere('email', 'like', '%' . $request->user . '%'));
        }

        if ($request->filled('endpoint')) {
            $query->where('endpoint', 'like', '%' . $request->endpoint . '%');
        }

        if ($request->filled('status')) {
            $query->where('status_code', $request->status);
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        $logs = $query->paginate(50)->withQueryString();

        return view('backend.modules.api-logs.index', compact('logs'));
    }
}
