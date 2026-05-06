<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

class MissingModuleController extends Controller
{
    public function __invoke()
    {
        abort(501, 'This module route is registered, but its controller implementation is missing from this codebase.');
    }
}
