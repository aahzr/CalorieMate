<!DOCTYPE html>
<html>
<head>
    <title>Laporan & Wawasan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .card { border: 1px solid #ddd; margin-bottom: 20px; padding: 15px; }
        .text-primary { color: #0d6efd; }
        .text-secondary { color: #6c757d; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f8f9fa; }
        .badge { padding: 5px 10px; border-radius: 10px; }
        .bg-light-green { background-color: #e8f5e9; }
    </style>
</head>
<body>
    <h1 class="text-primary">Laporan & Wawasan</h1>
    <p class="text-secondary">Tanggal Dibuat: {{ $reportDate }}</p>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-tint text-primary"></i>
                        <span class="fs-6 fw-semibold text-primary">Kalori Harian (Mingguan)</span>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kalori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calorieData as $data)
                                <tr>
                                    <td>{{ $data->date->format('d M Y') }}</td>
                                    <td>{{ $data->total_calories }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-camera text-primary"></i>
                        <span class="fs-6 fw-semibold text-primary">Perubahan Berat Badan</span>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Berat (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weightData as $data)
                                <tr>
                                    <td>{{ $data->date->format('d M Y') }}</td>
                                    <td>{{ $data->weight }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-bolt text-primary fs-3 mb-2"></i>
                    <p class="fs-3 fw-semibold text-primary">{{ $streak }}</p>
                    <p class="fs-6 text-secondary">Hari Terbaik Berturut-turut</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-utensils text-primary fs-3 mb-2"></i>
                    <p class="fs-3 fw-semibold text-primary">{{ $completeDays }}</p>
                    <p class="fs-6 text-secondary">Hari Catatan Lengkap</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-chart-line text-primary fs-3 mb-2"></i>
                    <p class="fs-3 fw-semibold text-primary">{{ $weightChange ? abs($weightChange) . ' kg' : '0 kg' }}</p>
                    <p class="fs-6 text-secondary">Berat Badan {{ $calorieGoalType == 'deficit' ? 'Turun' : 'Naik' }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-utensils text-primary"></i>
                        <span class="fs-6 fw-semibold text-primary">Rangkuman Makanan Favorit</span>
                    </div>
                    <table class="table">
                        <thead>
                            <tr class="text-secondary">
                                <th scope="col" class="text-start">#</th>
                                <th scope="col" class="text-start">Makanan</th>
                                <th scope="col" class="text-end">Konsumsi</th>
                                <th scope="col" class="text-end">Kalori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($favoriteFoods as $index => $food)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $food->food_name }}</td>
                                    <td class="text-end">{{ $food->count }}x</td>
                                    <td class="text-end">{{ $food->total_calories }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="far fa-check-circle text-primary"></i>
                        <span class="fs-6 fw-semibold text-primary">Capaian Target Mingguan</span>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-light-green d-flex justify-content-between align-items-center">
                            <span>Target Kalori Harian</span>
                            <span class="badge bg-primary text-white rounded-pill">{{ $calorieData->count() }}/7</span>
                        </li>
                        <li class="list-group-item bg-light-green d-flex justify-content-between align-items-center">
                            <span>Target Berat {{ $calorieGoalType == 'deficit' ? 'Turun' : 'Naik' }}</span>
                            <span class="badge bg-primary text-white rounded-pill">
                                {{ number_format(abs($weightChange ?? 0), 1) }} kg dari {{ number_format(0.5, 1) }} kg
                            </span>
                        </li>
                        <li class="list-group-item bg-light-green d-flex justify-content-between align-items-center">
                            <span>Catat Makan Lengkap</span>
                            <span class="badge bg-primary text-white rounded-pill">{{ $completeDays }}/7</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>