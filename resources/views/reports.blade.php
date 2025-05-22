@extends('layouts.app')

@section('title', 'Reports & Insights')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-4 fw-semibold text-primary"><i class="fas fa-chart-line me-2"></i> Laporan & Wawasan</h1>
                <button class="btn btn-primary"><i class="far fa-file-pdf me-1"></i> Ekspor PDF</button>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-tint text-primary"></i>
                        <span class="fs-6 fw-semibold text-primary">Kalori Harian (Mingguan)</span>
                    </div>
                    <canvas id="calorieChart" style="height: 200px;"></canvas>
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
                    <canvas id="weightChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-bolt text-primary fs-3 mb-2"></i>
                    <p class="fs-3 fw-semibold text-primary">8</p>
                    <p class="fs-6 text-secondary">Hari Terbaik Berturut-turut</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-utensils text-primary fs-3 mb-2"></i>
                    <p class="fs-3 fw-semibold text-primary">27</p>
                    <p class="fs-6 text-secondary">Hari Catatan Lengkap</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-chart-line text-primary fs-3 mb-2"></i>
                    <p class="fs-3 fw-semibold text-primary">3.2 kg</p>
                    <p class="fs-6 text-secondary">Berat Badan Turun</p>
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
                    <table class="table table-borderless fs-6">
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
                            <span class="badge bg-primary text-white rounded-pill">6/7</span>
                        </li>
                        <li class="list-group-item bg-light-green d-flex justify-content-between align-items-center">
                            <span>Target Berat Turun</span>
                            <span class="badge bg-primary text-white rounded-pill">2/2 kg</span>
                        </li>
                        <li class="list-group-item bg-light-green d-flex justify-content-between align-items-center">
                            <span>Catat Makan Lengkap</span>
                            <span class="badge bg-primary text-white rounded-pill">5/7</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const ctxCalorie = document.getElementById('calorieChart').getContext('2d');
        new Chart(ctxCalorie, {
            type: 'line',
            data: {
                labels: [@foreach($calorieData as $data)'{{ $data->date->format('d M') }}', @endforeach],
                datasets: [{
                    label: 'Kalori',
                    data: [@foreach($calorieData as $data){{ $data->total_calories }}, @endforeach],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        const ctxWeight = document.getElementById('weightChart').getContext('2d');
        new Chart(ctxWeight, {
            type: 'line',
            data: {
                labels: [@foreach($weightData as $data)'{{ $data->date->format('d M') }}', @endforeach],
                datasets: [{
                    label: 'Berat (kg)',
                    data: [@foreach($weightData as $data){{ $data->weight }}, @endforeach],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: false }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
@endsection