<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('superadmin.settings');
    }
}
