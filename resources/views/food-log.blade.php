@extends('layouts.app')

@section('title', 'Food Log')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-4 fw-semibold text-primary">Food Log</h1>
                <div class="d-flex align-items-center gap-3">
                    <button aria-label="Previous day" class="btn btn-link text-primary"><i class="fas fa-chevron-left"></i></button>
                    <span class="fs-6 fw-semibold text-primary">{{ $date->format('D, d M Y') }}</span>
                    <button aria-label="Next day" class="btn btn-link text-primary"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="fs-6 fw-semibold text-primary">Kalori Harian</p>
                        <p class="fs-6 text-secondary">Target: <span class="fw-bold">{{ $calorieGoal }} kcal</span></p>
                    </div>
                    <div class="progress">
                        <div class="progress-bar text-white fw-semibold" role="progressbar" style="width: {{ ($caloriesToday / $calorieGoal) * 100 }}%;" aria-valuenow="{{ $caloriesToday }}" aria-valuemin="0" aria-valuemax="{{ $calorieGoal }}">
                            {{ $caloriesToday }} kcal
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card bg-light-green">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-bolt text-primary"></i>
                        <span class="fs-6 fw-semibold text-primary">Sisa Hari Ini</span>
                    </div>
                    <p class="fs-3 fw-bold text-primary">
                        {{ $calorieGoal - $caloriesToday }} <span class="fs-5 fw-normal">kcal</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12">
            <form action="{{ route('food-log.store') }}" method="POST" class="card mb-4">
                @csrf
                <div class="card-body">
                    <h3 class="fs-5 fw-semibold text-primary mb-3">Tambah Makanan</h3>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <select name="meal_type" class="form-select" required>
                                <option value="" disabled selected>Pilih Tipe Makanan</option>
                                <option value="Breakfast">Sarapan</option>
                                <option value="Lunch">Makan Siang</option>
                                <option value="Dinner">Makan Malam</option>
                                <option value="Snack">Camilan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <input type="text" name="food_name" class="form-control" placeholder="Nama Makanan" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <input type="number" name="calories" class="form-control" placeholder="Kalori (kcal)" min="0" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Tambah</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @foreach(['Breakfast', 'Lunch', 'Dinner', 'Snack'] as $mealType)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas {{ $mealType === 'Breakfast' ? 'fa-egg' : ($mealType === 'Lunch' ? 'fa-drumstick-bite' : ($mealType === 'Dinner' ? 'fa-fish' : 'fa-apple-alt')) }} text-primary"></i>
                                <span class="fs-6 fw-semibold text-primary">{{ $mealType }}</span>
                            </div>
                            <a href="#" class="text-primary fs-6"><i class="fas fa-edit"></i></a>
                        </div>
                        <div class="bg-light-green rounded-3 p-3">
                            @foreach($foodLogs->get($mealType, []) as $food)
                                <p class="d-flex justify-content-between fs-6">
                                    <span>{{ $food->food_name }}</span>
                                    <span class="fw-semibold">{{ $food->calories }} <span class="text-secondary">kcal</span></span>
                                </p>
                            @endforeach
                        </div>
                        <p class="mt-2 fs-6 text-secondary">Total <span class="fw-semibold text-primary">{{ $foodLogs->get($mealType, collect())->sum('calories') }} kcal</span></p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection