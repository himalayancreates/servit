<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InvitationsController extends Controller
{
    public function index(): View
    {
        $invitations = Invitation::on('servit')
            ->with('invitedBy')
            ->latest()
            ->paginate(25);

        return view('superadmin.invitations.index', compact('invitations'));
    }

    public function resend(Invitation $invitation): RedirectResponse
    {
        if ($invitation->accepted_at) {
            return back()->with('error', 'This invitation has already been accepted.');
        }

        $invitation->update(['expires_at' => now()->addDays(7)]);

        Mail::send(new InvitationMail($invitation));

        return back()->with('success', "Invitation resent to {$invitation->email}.");
    }

    public function revoke(Invitation $invitation): RedirectResponse
    {
        if ($invitation->accepted_at) {
            return back()->with('error', 'Cannot revoke an accepted invitation.');
        }

        $invitation->update(['expires_at' => now()->subSecond()]);

        return back()->with('success', "Invitation to {$invitation->email} revoked.");
    }
}
