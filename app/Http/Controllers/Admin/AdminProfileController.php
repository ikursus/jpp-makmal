<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load('district');

        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        return back()->with('success', 'Profil berjaya dikemaskini.');
    }

    /**
     * Download the authenticated admin's profile in PDF format.
     */
    public function downloadPdf(): \Illuminate\Http\Response
    {
        $user = Auth::user();

        $user->load([
            'district',
            'roles',
            'loanApplications' => function ($query) {
                $query->latest();
            },
            'loanApplications.items.item',
            'loans' => function ($query) {
                $query->latest();
            },
            'loans.items.item',
        ]);

        $pdf = Pdf::loadView('pdf.user-profile', compact('user'));

        return $pdf->download('Profil_Saya_'.str_replace(' ', '_', $user->name).'.pdf');
    }
}
