<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $client = app('auth')->guard('client')->user();
        $tenant = $client->tenant()->with('plan')->first();

        $plans = Plan::on('servit')->where('is_active', true)->get();

        return view('portal.dashboard', compact('client', 'tenant', 'plans'));
    }
}
