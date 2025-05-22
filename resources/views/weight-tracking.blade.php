@extends('layouts.app')

@section('title', 'Weight Tracking')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex justify-content-end align-items-center gap-3 mb-4">
                <button aria-label="Previous week" class="btn btn-link text-primary"><i class="fas fa-chevron-left"></i></button>
                <span class="fs-6 fw-semibold text-primary">{{ $formattedWeek }}</span>
                <button aria-label="Next week" class="btn btn-link text-primary"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-lock text-primary fs-5"></i>
                        <span class="fs-5 fw-semibold text-primary">Berat Saat Ini</span>
                    </div>
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="fs-2 fw-bold text-primary">{{ $currentWeight ? $currentWeight->weight : 'N/A' }}</span>
                        <span class="fs-5 text-primary">kg</span>
                    </div>
                    <p class="fs-6 text-secondary mt-2">
                        Terakhir diperbarui: <span class="fw-semibold">{{ $currentWeight ? $currentWeight->formatted_date : 'N/A' }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card bg-light-green">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-bullseye text-primary fs-5"></i>
                        <span class="fs-5 fw-semibold text-primary">Target Berat</span>
                    </div>
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="fs-2 fw-bold text-primary">{{ $targetWeight }}</span>
                        <span class="fs-5 text-primary">kg</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <span class="badge bg-primary text-white">{{ $progress }}% progres</span>
                        <div class="progress flex-grow-1">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="fs-6 text-secondary">{{ $currentWeight ? ($currentWeight->weight - $targetWeight) : 'N/A' }} kg lagi</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <form action="{{ route('weight-tracking.store') }}" method="POST" class="card mb-4">
                @csrf
                <div class="card-body">
                    <h3 class="fs-5 fw-semibold text-primary mb-3">Tambah Berat</h3>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror" placeholder="Berat (kg)" step="0.1" min="0" value="{{ old('weight') }}" required>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ $date->format('Y-m-d') }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-primary">Tambah</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fs-5 fw-semibold text-primary">Log Berat</h2>
                        <a class="text-primary fs-6" href="#">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($weightLogs as $log)
                            <li class="list-group-item bg-light-green d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 12px; height: 12px;"></span>
                                    <span class="fw-semibold">{{ $log->weight }} kg</span>
                                </div>
                                <span class="text-secondary">{{ $log->formatted_date }}</span>
                            </li>
                        @empty
                            <li class="list-group-item bg-light-green">Belum ada log berat.</li>
                        @endforelse
                    </ul>
                    <p class="fs-6 text-primary mt-3"><i class="fas fa-info-circle me-1"></i> Catat berat badan rutin untuk melihat progres.</p>
                </div>
            </div>
        </div>
    </div>
@endsection