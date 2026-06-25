<div x-data="statisticsPage(@js($this->getChartData()))"
     @chart-data-updated.window="updateData($event.detail)"
     class="space-y-6">

    <!-- CSS Animasi & Gaya Tambahan -->
    <style>
        .custom-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }
        .custom-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: transparent;
            transition: background 0.3s ease;
        }
        .custom-card.accent-primary::before { background: linear-gradient(90deg, #3b82f6, #6366f1); }
        .custom-card.accent-success::before { background: linear-gradient(90deg, #10b981, #059669); }
        .custom-card.accent-warning::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .custom-card.accent-info::before { background: linear-gradient(90deg, #06b6d4, #0891b2); }
        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .tab-btn {
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tab-btn.active {
            background-color: #003366;
            color: white;
        }
        .tab-btn:not(.active) {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        .tab-btn:not(.active):hover {
            background-color: #e5e7eb;
        }
    </style>

    <!-- Penapis (Filters) Card -->
    <div class="custom-card accent-primary animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1f2937;">🔍 Penapis Statistik</h3>
                <p style="font-size: 13px; color: #6b7280; margin-top: 4px;">Pilih tahun dan bulan untuk menapis laporan grafik dan data tabulasi.</p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <!-- Penapis Tahun -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="year-select" style="font-size: 14px; font-weight: 600; color: #374151;">Tahun:</label>
                    <select id="year-select" wire:model.live="selectedYear" class="form-control" style="width: 110px; margin-bottom: 0;">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Penapis Bulan -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="month-select" style="font-size: 14px; font-weight: 600; color: #374151;">Bulan:</label>
                    <select id="month-select" wire:model.live="selectedMonth" class="form-control" style="width: 150px; margin-bottom: 0;">
                        <option value="">Semua Bulan</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Mac</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Jun</option>
                        <option value="7">Julai</option>
                        <option value="8">Ogos</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Disember</option>
                    </select>
                </div>

                <!-- Butang Reset -->
                <button wire:click="resetFilters" class="btn btn-secondary" style="height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 0 16px;">
                    <span>⟳</span> Set Semula
                </button>

                <!-- Butang Cetak -->
                <button onclick="window.print()" class="btn btn-primary" style="height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 0 16px;">
                    <span>🖨️</span> Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Ringkasan KPI Grid -->
    <div class="grid grid-4 animate-fade-in" style="margin-top: 24px;">
        <!-- Kad Pengguna Aktif -->
        <div class="custom-card accent-primary">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Jumlah Pengguna</p>
                    <h2 style="font-size: 32px; font-weight: 800; color: #1e3a8a; margin-top: 8px;">{{ number_format($kpi['total_users']) }}</h2>
                    <p style="font-size: 11px; color: #10b981; margin-top: 6px; font-weight: 600;">● Pengguna Aktif Sistem</p>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; font-size: 24px; color: #3b82f6;">
                    👥
                </div>
            </div>
        </div>

        <!-- Kad Jumlah Permohonan -->
        <div class="custom-card accent-warning">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Jumlah Permohonan</p>
                    <h2 style="font-size: 32px; font-weight: 800; color: #92400e; margin-top: 8px;">{{ number_format($kpi['total_applications']) }}</h2>
                    <p style="font-size: 11px; color: #6b7280; margin-top: 6px;">Dalam tempoh ditapis</p>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; font-size: 24px; color: #f59e0b;">
                    📋
                </div>
            </div>
        </div>

        <!-- Kad Jumlah Pinjaman Diluluskan -->
        <div class="custom-card accent-success">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Pinjaman Diproses</p>
                    <h2 style="font-size: 32px; font-weight: 800; color: #065f46; margin-top: 8px;">{{ number_format($kpi['total_approved_loans']) }}</h2>
                    <p style="font-size: 11px; color: #6b7280; margin-top: 6px;">Jumlah pinjaman dijana</p>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; font-size: 24px; color: #10b981;">
                    ✔️
                </div>
            </div>
        </div>

        <!-- Kad Pinjaman Aktif Semasa -->
        <div class="custom-card accent-info">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Pinjaman Aktif</p>
                    <h2 style="font-size: 32px; font-weight: 800; color: #155e75; margin-top: 8px;">{{ number_format($kpi['active_loans']) }}</h2>
                    <p style="font-size: 11px; color: #ef4444; margin-top: 6px; font-weight: 600;">● Sedang dipinjam keluar</p>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(6, 182, 212, 0.1); display: flex; align-items: center; justify-content: center; font-size: 24px; color: #06b6d4;">
                    📦
                </div>
            </div>
        </div>
    </div>

    <!-- Carta Analisis Grid -->
    <div class="grid grid-3 animate-fade-in" style="margin-top: 24px;">
        <!-- Carta 1: Trend Siri Masa -->
        <div class="custom-card accent-primary" style="grid-column: span 2; height: 400px; display: flex; flex-direction: column;" wire:ignore>
            <h3 style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 16px;">
                📈 Trend Permohonan vs Pinjaman
                <span style="font-size: 12px; font-weight: 400; color: #6b7280;">({{ $selectedMonth ? 'Harian' : 'Bulanan' }} bagi {{ $selectedYear }})</span>
            </h3>
            <div style="flex-grow: 1; position: relative;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Carta 2: Status Doughnut -->
        <div class="custom-card accent-warning" style="height: 400px; display: flex; flex-direction: column;" wire:ignore>
            <h3 style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 16px;">
                📊 Pecahan Status Permohonan
            </h3>
            <div style="flex-grow: 1; position: relative; display: flex; align-items: center; justify-content: center;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Carta Tambahan (Daerah) & Jadual Top Pengguna -->
    <div class="grid grid-2 animate-fade-in" style="margin-top: 24px; align-items: start;">
        <!-- Carta 3: Pengguna berdasarkan Daerah (Bar Mendatar) -->
        <div class="custom-card accent-primary" style="height: 480px; display: flex; flex-direction: column;" wire:ignore>
            <h3 style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 16px;">
                🗺️ Jumlah Pengguna Mengikut Daerah
            </h3>
            <div style="flex-grow: 1; position: relative;">
                <canvas id="districtChart"></canvas>
            </div>
        </div>

        <!-- Jadual 1: Statistik Pengguna (Toggling Antara Tab Pinjaman & Permohonan) -->
        <div class="custom-card accent-success" style="min-height: 480px; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #374151;">🏆 Senarai 10 Pengguna Aktif</h3>
                <div style="display: flex; gap: 6px;">
                    <button type="button" wire:click="$set('activeTab', 'loans')" class="tab-btn {{ $activeTab === 'loans' ? 'active' : '' }}">
                        Pinjaman
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'applications')" class="tab-btn {{ $activeTab === 'applications' ? 'active' : '' }}">
                        Permohonan
                    </button>
                </div>
            </div>

            <div class="table-responsive" style="flex-grow: 1;">
                @if($activeTab === 'loans')
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Pengguna</th>
                                <th>Daerah</th>
                                <th style="text-align: center;">Jumlah Pinjaman</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userLoanStats as $index => $u)
                                <tr>
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>
                                        <div style="font-weight: 600; color: #111827;">{{ $u->name }}</div>
                                        <div style="font-size: 11px; color: #6b7280;">{{ $u->email }}</div>
                                    </td>
                                    <td>{{ $u->district->name ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge badge-success" style="font-size: 13px; padding: 4px 10px;">{{ $u->loans_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #9ca3af; padding: 30px 0;">Tiada data rekod pinjaman bagi tempoh ditapis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Pengguna</th>
                                <th>Daerah</th>
                                <th style="text-align: center;">Jumlah Permohonan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userAppStats as $index => $u)
                                <tr>
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>
                                        <div style="font-weight: 600; color: #111827;">{{ $u->name }}</div>
                                        <div style="font-size: 11px; color: #6b7280;">{{ $u->email }}</div>
                                    </td>
                                    <td>{{ $u->district->name ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge badge-info" style="font-size: 13px; padding: 4px 10px;">{{ $u->loan_applications_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #9ca3af; padding: 30px 0;">Tiada data rekod permohonan bagi tempoh ditapis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Jadual 2: Perincian Daerah Lengkap -->
    <div class="custom-card accent-primary animate-fade-in" style="margin-top: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 16px;">📊 Perincian Data Mengikut Daerah</h3>
        <div class="table-responsive">
            <table class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nama Daerah</th>
                        <th style="text-align: center;">Kod</th>
                        <th style="text-align: center;">Jumlah Pengguna</th>
                        <th style="text-align: center;">Jumlah Permohonan</th>
                        <th style="text-align: center;">Jumlah Pinjaman</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($districtStats as $d)
                        <tr>
                            <td style="font-weight: 600; color: #111827;">{{ $d->name }}</td>
                            <td style="text-align: center;"><span class="badge badge-secondary">{{ $d->code }}</span></td>
                            <td style="text-align: center; font-weight: bold; color: #4f46e5;">{{ $d->users_count }}</td>
                            <td style="text-align: center; font-weight: bold; color: #d97706;">{{ $d->loan_applications_count }}</td>
                            <td style="text-align: center; font-weight: bold; color: #059669;">{{ $d->loans_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #9ca3af; padding: 30px 0;">Tiada rekod daerah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AlpineJS & Chart.js Integration Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('statisticsPage', (initialData) => ({
                data: Array.isArray(initialData) ? initialData[0] : initialData,

                init() {
                    this._waitForChart();
                },

                _waitForChart() {
                    if (typeof Chart === 'undefined') {
                        setTimeout(() => this._waitForChart(), 50);
                        return;
                    }
                    this.$nextTick(() => {
                        this._initTrend();
                        this._initDistrict();
                        this._initStatus();
                    });
                },

                updateData(newData) {
                    this.data = Array.isArray(newData) ? newData[0] : newData;
                    this._updateTrend();
                    this._updateDistrict();
                    this._updateStatus();
                },

                // ── Trend Line Chart ──────────────────────────────
                _initTrend() {
                    const el = document.getElementById('trendChart');
                    if (!el) return;
                    el._chart = new Chart(el.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: this.data.labels,
                            datasets: [
                                {
                                    label: 'Jumlah Permohonan',
                                    data: this.data.applications,
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'rgba(59,130,246,0.08)',
                                    borderWidth: 3, tension: 0.3, fill: true,
                                    pointBackgroundColor: '#3b82f6', pointRadius: 4
                                },
                                {
                                    label: 'Jumlah Pinjaman',
                                    data: this.data.loans,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16,185,129,0.08)',
                                    borderWidth: 3, tension: 0.3, fill: true,
                                    pointBackgroundColor: '#10b981', pointRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { position: 'top', labels: { font: { weight: 'bold' } } } },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1, callback: v => Math.floor(v) === v ? v : undefined }, grid: { color: 'rgba(0,0,0,0.05)' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                },
                _updateTrend() {
                    const c = document.getElementById('trendChart')?._chart;
                    if (!c) return;
                    c.data.labels = this.data.labels;
                    c.data.datasets[0].data = this.data.applications;
                    c.data.datasets[1].data = this.data.loans;
                    c.update();
                },

                // ── District Horizontal Bar Chart ─────────────────
                _initDistrict() {
                    const el = document.getElementById('districtChart');
                    if (!el) return;
                    el._chart = new Chart(el.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: this.data.districts,
                            datasets: [{
                                label: 'Jumlah Pengguna',
                                data: this.data.districtCounts,
                                backgroundColor: 'rgba(99,102,241,0.85)',
                                hoverBackgroundColor: '#6366f1',
                                borderRadius: 6, borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { beginAtZero: true, ticks: { stepSize: 1, callback: v => Math.floor(v) === v ? v : undefined }, grid: { color: 'rgba(0,0,0,0.05)' } },
                                y: { grid: { display: false } }
                            }
                        }
                    });
                },
                _updateDistrict() {
                    const c = document.getElementById('districtChart')?._chart;
                    if (!c) return;
                    c.data.labels = this.data.districts;
                    c.data.datasets[0].data = this.data.districtCounts;
                    c.update();
                },

                // ── Status Doughnut Chart ─────────────────────────
                _initStatus() {
                    const el = document.getElementById('statusChart');
                    if (!el) return;
                    el._chart = new Chart(el.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: this.data.statusLabels,
                            datasets: [{
                                data: this.data.statusCounts,
                                backgroundColor: ['#f59e0b','#10b981','#ef4444','#6b7280','#3b82f6','#8b5cf6'],
                                borderWidth: 2, hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
                            cutout: '60%'
                        }
                    });
                },
                _updateStatus() {
                    const c = document.getElementById('statusChart')?._chart;
                    if (!c) return;
                    c.data.labels = this.data.statusLabels;
                    c.data.datasets[0].data = this.data.statusCounts;
                    c.update();
                }
            }));
        });
    </script>
</div>



