<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{

    public function jsonSuccess()
    {
        return response()->json([
            'success' => true,
            'status' => 200
        ]);
    }

    public function jsonError()
    {
        return response()->json([
            'success' => false,
        ]);
    }
}
