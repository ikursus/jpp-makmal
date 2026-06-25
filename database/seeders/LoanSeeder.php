<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\District;
use App\Models\Loan;
use App\Models\LoanApplication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        // Padam data sedia ada untuk elakkan perlanggaran kekunci unik
        Loan::query()->delete();
        LoanApplication::query()->delete();

        $users = User::role('user')->get();
        $admin = User::role('admin')->first() ?? User::role('super_admin')->first() ?? User::first();

        if ($users->isEmpty()) {
            $this->command->error('Tiada pengguna peranan "user" dijumpai. Sila jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $purposes = [
            'Peralatan kerja lapangan daerah',
            'Program gotong-royong komuniti',
            'Majlis perasmian dewan daerah',
            'Kursus latihan IT kakitangan',
            'Pameran pertanian daerah',
            'Program kesihatan dan kecergasan',
            'Aktiviti sukan tahunan JPP',
            'Projek inventori makmal daerah',
        ];

        $totalApplications = 250;
        $now = Carbon::now();

        $this->command->info("Menjana {$totalApplications} permohonan rawak...");

        for ($i = 1; $i <= $totalApplications; $i++) {
            // Pilih user rawak
            $user = $users->random();
            $districtId = $user->district_id;

            // Jana tarikh rawak dalam tempoh 12 bulan ke belakang
            $monthsAgo = mt_rand(0, 11);
            $daysAgo = mt_rand(1, 28);
            $startDate = (clone $now)->subMonths($monthsAgo)->subDays($daysAgo)->startOfDay();
            $endDate = (clone $startDate)->addDays(mt_rand(5, 14))->endOfDay();

            // Taburan status (Kembalikan/Dipinjam ~ 65%, Diluluskan ~ 15%, Menunggu ~ 10%, Ditolak/Batal ~ 10%)
            $rand = mt_rand(1, 100);
            if ($rand <= 40) {
                $status = 'dikembalikan';
            } elseif ($rand <= 65) {
                $status = 'dipinjam';
            } elseif ($rand <= 80) {
                $status = 'diluluskan';
            } elseif ($rand <= 90) {
                $status = 'menunggu';
            } elseif ($rand <= 95) {
                $status = 'ditolak';
            } else {
                $status = 'dibatalkan';
            }

            $appNo = 'APP-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $app = LoanApplication::create([
                'application_no' => $appNo,
                'user_id' => $user->id,
                'district_id' => $districtId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'purpose' => $purposes[array_rand($purposes)],
                'status' => $status,
                'rejection_reason' => $status === 'ditolak' ? 'Dokumen sokongan tidak lengkap.' : null,
                'approved_by' => in_array($status, ['diluluskan', 'dipinjam', 'dikembalikan', 'ditolak']) ? $admin->id : null,
                'approved_at' => in_array($status, ['diluluskan', 'dipinjam', 'dikembalikan', 'ditolak']) ? (clone $startDate)->addHours(mt_rand(2, 24)) : null,
                'created_at' => (clone $startDate)->subDays(mt_rand(1, 3)),
                'updated_at' => $startDate,
            ]);

            // Bina rekod Pinjaman (Loan) jika berkaitan
            if (in_array($status, ['diluluskan', 'dipinjam', 'dikembalikan'])) {
                $loanNo = 'LOAN-' . str_pad($i, 5, '0', STR_PAD_LEFT);
                
                // Tentukan status pinjaman
                if ($status === 'dikembalikan') {
                    $loanStatus = 'dipulangkan';
                    $actualReturn = (clone $endDate)->subDays(mt_rand(-2, 2));
                } else {
                    // status dipinjam atau diluluskan
                    if ($endDate->isPast()) {
                        $loanStatus = 'terlewat';
                    } else {
                        $loanStatus = 'aktif';
                    }
                    $actualReturn = null;
                }

                Loan::create([
                    'loan_no' => $loanNo,
                    'loan_application_id' => $app->id,
                    'user_id' => $user->id,
                    'district_id' => $districtId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'actual_return_date' => $actualReturn,
                    'status' => $loanStatus,
                    'notes' => 'Pinjaman dijana secara automatik oleh seeder.',
                    'created_by' => $admin->id,
                    'created_at' => $app->approved_at ?? $startDate,
                    'updated_at' => $startDate,
                ]);
            }
        }

        $this->command->info('Data permohonan dan pinjaman rawak berjaya dijana!');
    }
}
