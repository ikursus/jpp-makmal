<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Users\UsersExport;
use App\Exports\Users\UsersQueuedExport;
use App\Exports\Users\UsersMultipleSheetsExport;
use App\Exports\Users\UsersExportWithView;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Export users to Excel (XLSX) — standard export with all formatting options.
     *
     * Supported formats: xlsx, csv, ods, xls, html, tsv, pdf, dompdf, mpdf, tcpdf
     */
    public function exportXlsx(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        return Excel::download(
            new UsersExport($filters, $sortField, $sortDirection),
            'senarai-pengguna.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Export users to CSV
     */
    public function exportCsv(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);

        return Excel::download(
            new UsersExport($filters),
            'senarai-pengguna.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Export users to ODS (OpenDocument Spreadsheet)
     */
    public function exportOds(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);

        return Excel::download(
            new UsersExport($filters),
            'senarai-pengguna.ods',
            \Maatwebsite\Excel\Excel::ODS
        );
    }

    /**
     * Export users to HTML
     */
    public function exportHtml(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);

        return Excel::download(
            new UsersExport($filters),
            'senarai-pengguna.html',
            \Maatwebsite\Excel\Excel::HTML
        );
    }

    /**
     * Export users to PDF (using DOMPDF)
     */
    public function exportPdf(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);

        return Excel::download(
            new UsersExport($filters),
            'senarai-pengguna.pdf',
            \Maatwebsite\Excel\Excel::DOMPDF
        );
    }

    /**
     * Export users to PDF (using TCPDF)
     */
    public function exportTcpdf(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);

        return Excel::download(
            new UsersExport($filters),
            'senarai-pengguna-tcpdf.pdf',
            \Maatwebsite\Excel\Excel::TCPDF
        );
    }

    /**
     * Export users to PDF (using MPDF)
     */
    public function exportMpdf(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);

        return Excel::download(
            new UsersExport($filters),
            'senarai-pengguna-mpdf.pdf',
            \Maatwebsite\Excel\Excel::MPDF
        );
    }

    /**
     * Export users using Blade view template (FromView)
     */
    public function exportView(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);

        return Excel::download(
            new UsersExportWithView($filters),
            'senarai-pengguna-view.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Export users with multiple sheets (one per district + summary)
     */
    public function exportMultipleSheets(): BinaryFileResponse
    {
        return Excel::download(
            new UsersMultipleSheetsExport(),
            'pengguna-mengikut-daerah.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Store export file on server instead of downloading directly
     */
    public function exportStore(Request $request): \Illuminate\Http\RedirectResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);
        $format = $request->get('format', 'xlsx');

        $filePath = 'exports/pengguna/' . date('Y-m-d_H-i-s') . '_senarai-pengguna.' . $format;

        Excel::store(
            new UsersExport($filters),
            $filePath,
            'local',
            \Maatwebsite\Excel\Excel::XLSX
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Fail berjaya disimpan: {$filePath}");
    }

    /**
     * Queue export for large datasets (runs in background)
     */
    public function exportQueue(Request $request): \Illuminate\Http\RedirectResponse
    {
        $districtId = $request->get('district_id');
        $role = $request->get('role');

        // The export will be processed in the background via queue
        Excel::queue(
            new UsersQueuedExport($districtId, $role),
            'exports/pengguna/queued_' . date('Y-m-d_H-i-s') . '.xlsx',
            'local',
            \Maatwebsite\Excel\Excel::XLSX
        )->chain([
            // Optional: send notification when export is complete
            // new \App\Notifications\ExportComplete(auth()->user()),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Eksport sedang diproses. Anda akan diberitahu setelah siap.');
    }

    /**
     * Export and return as a streamed response (for very large files)
     */
    public function exportStream(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'district_id', 'role', 'is_active']);

        return Excel::download(
            new UsersExport($filters),
            'senarai-pengguna-stream.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        )->deleteFileAfterSend(true);
    }

    public function create()
    {
        $districts = District::where('is_active', true)->get();
        $roles = Role::all();
        return view('admin.users.create', compact('districts', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:20',
            'district_id' => 'nullable|exists:districts,id',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'district_id' => $validated['district_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berjaya ditambah.');
    }

    public function edit(User $user)
    {
        $districts = District::where('is_active', true)->get();
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'districts', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'phone' => 'nullable|string|max:20',
            'district_id' => 'nullable|exists:districts,id',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'district_id' => $validated['district_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berjaya dikemaskini.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Super Admin tidak boleh dipadam.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berjaya dipadam.');
    }

    /**
     * Download the profile of a user in PDF format.
     */
    public function downloadProfilePdf(User $user): \Illuminate\Http\Response
    {
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

        return $pdf->download('Profil_' . str_replace(' ', '_', $user->name) . '.pdf');
    }
}
