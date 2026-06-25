<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>No. Telefon</th>
            <th>Daerah</th>
            <th>Peranan</th>
            <th>Status</th>
            <th>Log Masuk Terakhir</th>
            <th>Tarikh Daftar</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>{{ $user->district->name ?? '-' }}</td>
                <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                <td>{{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : '-' }}</td>
                <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align: center;">Tiada data pengguna.</td>
            </tr>
        @endforelse
    </tbody>
</table>
