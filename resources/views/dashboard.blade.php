@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    @auth
                        <h1 class="fs-4 fw-semibold text-primary">Halo, {{ Auth::user()->name }}!</h1>
                    @else
                        <h1 class="fs-4 fw-semibold text-primary">Halo, Pengguna!</h1>
                    @endauth
                    <p class="fs-6 text-secondary">Pantau progres diet harian Anda.</p>
                </div>
                @auth
                    <div class="d-flex gap-3">
                        <a href="{{ route('food-log') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Makanan</a>
                        <a href="{{ route('weight-tracking') }}" class="btn btn-outline-primary"><i class="fas fa-balance-scale me-1"></i> Tambah Berat</a>
                    </div>
                @endauth
            </div>
        </div>
        @auth
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-bolt text-primary fs-4"></i>
                                <h2 class="fs-5 fw-semibold text-primary">Kalori Hari Ini</h2>
                            </div>
                            <div class="fs-6 text-primary fw-semibold">Target: <span class="fw-bold">{{ $calorieGoal }} kcal</span></div>
                            <div class="flex-grow-1">
                                <div class="progress">
                                    <div class="progress-bar text-white fw-semibold" role="progressbar" style="width: {{ ($caloriesToday / $calorieGoal) * 100 }}%;" aria-valuenow="{{ $caloriesToday }}" aria-valuemin="0" aria-valuemax="{{ $calorieGoal }}">
                                        {{ $caloriesToday }} kcal
                                    </div>
                                </div>
                            </div>
                            <div class="text-primary fw-bold fs-3 text-md-end">
                                {{ round(($caloriesToday / $calorieGoal) * 100) }} <span class="fs-6 fw-semibold">%</span>
                                <p class="text-primary fw-normal fs-6 mt-1">dari target harian</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card bg-light-green text-center">
                    <div class="card-body">
                        <i class="fas fa-trophy text-primary fs-3 mb-2"></i>
                        <p class="fs-6 text-secondary mb-1">Streak</p>
                        <p class="fs-4 fw-bold text-primary">{{ $streak }} Hari</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card bg-light-green text-center">
                    <div class="card-body">
                        <i class="fas fa-fire-alt text-primary fs-3 mb-2"></i>
                        <p class="fs-6 text-secondary mb-1">Kalori Terbakar</p>
                        <p class="fs-4 fw-bold text-primary">{{ $caloriesBurned }} kcal</p>
                    </div>
                </div>
            </div>
        @endauth
    </div>
@endsection