<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Profil Pengguna</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header h1 {
            color: #1e3a8a;
            font-size: 18px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 0;
            color: #4b5563;
            font-size: 10px;
        }
        .section-title {
            background-color: #f1f5f9;
            color: #1e3a8a;
            font-size: 12px;
            font-weight: bold;
            padding: 6px 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #1e3a8a;
            text-transform: uppercase;
        }
        .profile-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .profile-table td {
            padding: 6px 8px;
            vertical-align: top;
        }
        .profile-table td.label {
            font-weight: bold;
            color: #4b5563;
            width: 25%;
            border-bottom: 1px solid #e2e8f0;
        }
        .profile-table td.value {
            border-bottom: 1px solid #e2e8f0;
            color: #1f2937;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        .data-table th {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .badge-warning {
            background-color: #fef9c3;
            color: #a16207;
            border: 1px solid #fef08a;
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .badge-secondary {
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        .items-list {
            margin: 0;
            padding-left: 12px;
        }
        .items-list li {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1>Ringkasan Profil Pengguna</h1>
                    <p>Jabatan Pembangunan Persekutuan (JPP) Makmal</p>
                </td>
                <td style="text-align: right; font-size: 10px; color: #6b7280; vertical-align: bottom;">
                    Tarikh Cetakan: {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Maklumat Profil Pengguna</div>
    <table class="profile-table">
        <tr>
            <td class="label">Nama Penuh</td>
            <td class="value">{{ $user->name }}</td>
            <td class="label">Peranan</td>
            <td class="value">{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat E-mel</td>
            <td class="value">{{ $user->email }}</td>
            <td class="label">Daerah</td>
            <td class="value">{{ $user->district->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Telefon</td>
            <td class="value">{{ $user->phone ?? '-' }}</td>
            <td class="label">Status Akaun</td>
            <td class="value">
                @if($user->is_active)
                    <span class="badge badge-success">Aktif</span>
                @else
                    <span class="badge badge-danger">Tidak Aktif</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Tarikh Daftar</td>
            <td class="value">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</td>
            <td class="label">Log Masuk Terakhir</td>
            <td class="value">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Rekod Permohonan Pinjaman</div>
    @if($user->loanApplications && $user->loanApplications->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">No. Permohonan</th>
                    <th style="width: 25%;">Tujuan & Tempoh</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 25%;">Item & Kuantiti</th>
                    <th style="width: 20%;">Maklumat Kelulusan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->loanApplications as $app)
                    <tr>
                        <td style="font-weight: bold;">{{ $app->application_no }}</td>
                        <td>
                            <strong>Tujuan:</strong> {{ $app->purpose }}<br>
                            <span style="color: #4b5563; font-size: 9px;">
                                {{ $app->start_date ? $app->start_date->format('d/m/Y') : '-' }} - 
                                {{ $app->end_date ? $app->end_date->format('d/m/Y') : '-' }}
                            </span>
                        </td>
                        <td>
                            @if($app->status === 'menunggu')
                                <span class="badge badge-warning">Menunggu</span>
                            @elseif($app->status === 'diluluskan')
                                <span class="badge badge-success">Diluluskan</span>
                            @elseif($app->status === 'ditolak')
                                <span class="badge badge-danger">Ditolak</span>
                            @elseif($app->status === 'dibatalkan')
                                <span class="badge badge-secondary">Dibatalkan</span>
                            @elseif($app->status === 'dipinjam')
                                <span class="badge badge-info">Dipinjam</span>
                            @elseif($app->status === 'dikembalikan')
                                <span class="badge badge-success">Dikembalikan</span>
                            @else
                                <span class="badge badge-secondary">{{ $app->status }}</span>
                            @endif
                        </td>
                        <td>
                            <ul class="items-list">
                                @forelse($app->items as $appItem)
                                    <li>
                                        {{ $appItem->item->name ?? 'Item tidak diketahui' }}
                                        <br>
                                        <small style="color: #6b7280;">
                                            Diminta: {{ $appItem->quantity_requested }}
                                            @if($appItem->quantity_approved !== null)
                                                | Diluluskan: {{ $appItem->quantity_approved }}
                                            @endif
                                        </small>
                                    </li>
                                @empty
                                    <li style="color: #9ca3af; list-style: none;">Tiada item disenaraikan</li>
                                @endforelse
                            </ul>
                        </td>
                        <td>
                            @if($app->status === 'diluluskan' || $app->status === 'dipinjam' || $app->status === 'dikembalikan')
                                <span style="font-size: 9px; color: #15803d;">
                                    <strong>Diluluskan Oleh:</strong><br>
                                    {{ $app->approvedBy->name ?? 'Sistem' }}<br>
                                    <strong>Tarikh:</strong><br>
                                    {{ $app->approved_at ? $app->approved_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            @elseif($app->status === 'ditolak')
                                <span style="font-size: 9px; color: #b91c1c;">
                                    <strong>Alasan Penolakan:</strong><br>
                                    {{ $app->rejection_reason ?: 'Tiada alasan diberikan' }}
                                </span>
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #6b7280; font-style: italic; padding-left: 10px;">Tiada rekod permohonan pinjaman dijumpai untuk pengguna ini.</p>
    @endif

    <div class="section-title">Rekod Pinjaman & Pemulangan</div>
    @if($user->loans && $user->loans->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">No. Pinjaman</th>
                    <th style="width: 25%;">Tempoh Pinjaman</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 25%;">Item & Kuantiti</th>
                    <th style="width: 20%;">Tarikh Pulang / Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->loans as $loan)
                    <tr>
                        <td style="font-weight: bold;">{{ $loan->loan_no }}</td>
                        <td>
                            {{ $loan->start_date ? $loan->start_date->format('d/m/Y') : '-' }} - 
                            {{ $loan->end_date ? $loan->end_date->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            @if($loan->status === 'aktif')
                                <span class="badge badge-info">Aktif</span>
                            @elseif($loan->status === 'dipulangkan')
                                <span class="badge badge-success">Dipulangkan</span>
                            @elseif($loan->status === 'terlewat')
                                <span class="badge badge-danger">Terlewat</span>
                            @else
                                <span class="badge badge-secondary">{{ $loan->status }}</span>
                            @endif
                        </td>
                        <td>
                            <ul class="items-list">
                                @forelse($loan->items as $lItem)
                                    <li>
                                        {{ $lItem->item->name ?? 'Item tidak diketahui' }}
                                        <br>
                                        <small style="color: #6b7280;">
                                            Dipinjam: {{ $lItem->quantity_loaned }}
                                            @if($lItem->quantity_returned > 0)
                                                | Dipulangkan: {{ $lItem->quantity_returned }}
                                            @endif
                                        </small>
                                        @if($lItem->condition_before || $lItem->condition_after)
                                            <br>
                                            <small style="color: #4b5563;">
                                                Kondisi Asal: {{ ucfirst($lItem->condition_before) }}
                                                @if($lItem->condition_after)
                                                    | Kondisi Pulang: {{ ucfirst($lItem->condition_after) }}
                                                @endif
                                            </small>
                                        @endif
                                    </li>
                                @empty
                                    <li style="color: #9ca3af; list-style: none;">Tiada item disenaraikan</li>
                                @endforelse
                            </ul>
                        </td>
                        <td>
                            @if($loan->actual_return_date)
                                <strong>Tarikh Dipulang:</strong><br>
                                {{ $loan->actual_return_date->format('d/m/Y') }}<br>
                            @endif
                            @if($loan->notes)
                                <strong>Nota:</strong><br>
                                <span style="font-size: 9px; color: #4b5563;">{{ $loan->notes }}</span>
                            @endif
                            @if(!$loan->actual_return_date && !$loan->notes)
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #6b7280; font-style: italic; padding-left: 10px;">Tiada rekod pinjaman dijumpai untuk pengguna ini.</p>
    @endif

    <div class="footer">
        Ringkasan Profil Pengguna JPP-Makmal - Fail ini dijana secara automatik.
    </div>

</body>
</html>
