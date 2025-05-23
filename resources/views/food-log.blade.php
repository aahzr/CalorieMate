@extends('layouts.app')

@section('title', 'Food Log')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-4 fw-semibold text-primary">Food Log</h1>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('food-log', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" aria-label="Previous day" class="btn btn-link text-primary"><i class="fas fa-chevron-left"></i></a>
                    <span class="fs-6 fw-semibold text-primary">{{ $date->format('D, d M Y') }}</span>
                    <a href="{{ route('food-log', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" aria-label="Next day" class="btn btn-link text-primary"><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <!-- Pencarian Makanan -->
        <div class="col-12">
            <form action="{{ route('food-log') }}" method="GET" class="card mb-4">
                <div class="card-body">
                    <h3 class="fs-5 fw-semibold text-primary mb-3">Cari Makanan (FatSecret)</h3>
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                            <input type="text" name="search_food" value="{{ $searchQuery ?? '' }}" class="form-control" placeholder="Cari makanan (misalnya, Chicken)" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-primary w-100" id="searchBtn">Cari</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Hasil Pencarian -->
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="fs-5 fw-semibold text-primary mb-3">Hasil Pencarian</h3>
                    <div id="loading" style="display: none;" class="text-center">
                        <p class="fs-6 text-secondary">Memuat...</p>
                    </div>
                    <div id="searchResults">
                        @if (!empty($foodResults))
                            @foreach ($foodResults as $food)
                                <div class="bg-light-green rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="fs-6 mb-2">{{ $food['food_name'] }} ({{ $food['brand_name'] ?? 'Generic' }})</p>
                                            <p class="fs-6 text-secondary mb-2">{{ $food['food_description'] }}</p>
                                        </div>
                                        <form action="{{ route('food-log.store') }}?date={{ $date->format('Y-m-d') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="food_id" value="{{ $food['food_id'] }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <select name="meal_type" class="form-select" style="width: 150px;" required>
                                                    <option value="Breakfast">Sarapan</option>
                                                    <option value="Lunch">Makan Siang</option>
                                                    <option value="Dinner">Makan Malam</option>
                                                    <option value="Snack">Camilan</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="fs-6 text-secondary">Tidak ada hasil ditemukan. Coba kueri lain seperti 'chicken' atau gunakan input manual di card.</p>
                            @if ($searchQuery)
                                <p class="fs-6 text-secondary">Kueri: {{ $searchQuery }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Kalori Harian -->
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
        <!-- Sisa Kalori -->
        <div class="col-12 col-md-6">
            <div class="card bg-light-green">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-bolt text-primary"></i>
                        <span class="fs-6 fw-semibold text-primary">Sisa Hari Ini</span>
                    </div>
                    <p class="fs-3 fw-bold text-primary">
                        {{ $remainingCalories }} <span class="fs-5 fw-normal">kcal</span>
                    </p>
                </div>
            </div>
        </div>
        <!-- Log Makanan -->
        @foreach(['Breakfast', 'Lunch', 'Dinner', 'Snack'] as $mealType)
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas {{ $mealType === 'Breakfast' ? 'fa-egg' : ($mealType === 'Lunch' ? 'fa-drumstick-bite' : ($mealType === 'Dinner' ? 'fa-fish' : 'fa-apple-alt')) }} text-primary"></i>
                                <span class="fs-5 fw-semibold text-primary">{{ $mealType }}</span>
                            </div>
                            <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#addFoodModal-{{ str_replace(' ', '-', $mealType) }}"><i class="fas fa-plus"></i></button>
                        </div>
                        @forelse($foodLogs->get($mealType, []) as $food)
                            <div class="bg-light-green rounded-3 p-3 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                                        <span class="fs-6 fw-semibold" style="min-width: 200px;">{{ $food->food_name }}</span>
                                        <span class="fs-6">
                                            {{ $food->calories }} kcal
                                            @if($food->carbohydrate !== null || $food->protein !== null || $food->fat !== null)
                                                (C: {{ $food->carbohydrate ?? '-' }}g, P: {{ $food->protein ?? '-' }}g, F: {{ $food->fat ?? '-' }}g)
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#editFoodModal-{{ $food->id }}"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('food-log.destroy', $food) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Hapus log ini?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="fs-6 text-secondary">Belum ada log.</p>
                        @endforelse
                        <p class="mt-3 fs-6 text-secondary">Total <span class="fw-semibold text-primary">{{ $foodLogs->get($mealType, collect())->sum('calories') }} kcal</span></p>
                    </div>
                </div>
            </div>
            <!-- Modal Tambah -->
            <div class="modal fade" id="addFoodModal-{{ str_replace(' ', '-', $mealType) }}" tabindex="-1" aria-labelledby="addFoodModalLabel-{{ str_replace(' ', '-', $mealType) }}">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addFoodModalLabel-{{ str_replace(' ', '-', $mealType) }}">Tambah {{ $mealType }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('food-log.store') }}?date={{ $date->format('Y-m-d') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="meal_type-{{ str_replace(' ', '-', $mealType) }}">Tipe Makanan</label>
                                    <input type="text" class="form-control" id="meal_type-{{ str_replace(' ', '-', $mealType) }}" name="meal_type" value="{{ $mealType }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="food_name-{{ str_replace(' ', '-', $mealType) }}">Nama Makanan</label>
                                    <input type="text" class="form-control @error('food_name') is-invalid @enderror" id="food_name-{{ str_replace(' ', '-', $mealType) }}" name="food_name" value="{{ old('food_name') }}" required>
                                    @error('food_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="calories-{{ str_replace(' ', '-', $mealType) }}">Kalori (kcal)</label>
                                    <input type="number" class="form-control @error('calories') is-invalid @enderror" id="calories-{{ str_replace(' ', '-', $mealType) }}" name="calories" value="{{ old('calories') }}" min="0" required>
                                    @error('calories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="carbohydrate-{{ str_replace(' ', '-', $mealType) }}">Karbohidrat (g)</label>
                                    <input type="number" step="0.01" class="form-control @error('carbohydrate') is-invalid @enderror" id="carbohydrate-{{ str_replace(' ', '-', $mealType) }}" name="carbohydrate" value="{{ old('carbohydrate') }}" min="0">
                                    @error('carbohydrate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="protein-{{ str_replace(' ', '-', $mealType) }}">Protein (g)</label>
                                    <input type="number" step="0.01" class="form-control @error('protein') is-invalid @enderror" id="protein-{{ str_replace(' ', '-', $mealType) }}" name="protein" value="{{ old('protein') }}" min="0">
                                    @error('protein')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="fat-{{ str_replace(' ', '-', $mealType) }}">Lemak (g)</label>
                                    <input type="number" step="0.01" class="form-control @error('fat') is-invalid @enderror" id="fat-{{ str_replace(' ', '-', $mealType) }}" name="fat" value="{{ old('fat') }}" min="0">
                                    @error('fat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal Edit -->
            @if($foodLogs->get($mealType, []))
                @foreach($foodLogs->get($mealType, []) as $food)
                    <div class="modal fade" id="editFoodModal-{{ $food->id }}" tabindex="-1" aria-labelledby="editFoodModalLabel-{{ $food->id }}">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editFoodModalLabel-{{ $food->id }}">Edit {{ $food->food_name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('food-log.update', $food) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="meal_type-{{ $food->id }}">Tipe Makanan</label>
                                            <select class="form-select @error('meal_type') is-invalid @enderror" id="meal_type-{{ $food->id }}" name="meal_type" required>
                                                @foreach(['Breakfast', 'Lunch', 'Dinner', 'Snack'] as $type)
                                                    <option value="{{ $type }}" {{ $food->meal_type == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @error('meal_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="food_name-{{ $food->id }}">Nama Makanan</label>
                                            <input type="text" class="form-control @error('food_name') is-invalid @enderror" id="food_name-{{ $food->id }}" name="food_name" value="{{ old('food_name', $food->food_name) }}" required>
                                            @error('food_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="calories-{{ $food->id }}">Kalori (kcal)</label>
                                            <input type="number" class="form-control @error('calories') is-invalid @enderror" id="calories-{{ $food->id }}" name="calories" value="{{ old('calories', $food->calories) }}" min="0" required>
                                            @error('calories')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="carbohydrate-{{ $food->id }}">Karbohidrat (g)</label>
                                            <input type="number" step="0.01" class="form-control @error('carbohydrate') is-invalid @enderror" id="carbohydrate-{{ $food->id }}" name="carbohydrate" value="{{ old('carbohydrate', $food->carbohydrate) }}" min="0">
                                            @error('carbohydrate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="protein-{{ $food->id }}">Protein (g)</label>
                                            <input type="number" step="0.01" class="form-control @error('protein') is-invalid @enderror" id="protein-{{ $food->id }}" name="protein" value="{{ old('protein', $food->protein) }}" min="0">
                                            @error('protein')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="fat-{{ $food->id }}">Lemak (g)</label>
                                            <input type="number" step="0.01" class="form-control @error('fat') is-invalid @enderror" id="fat-{{ $food->id }}" name="fat" value="{{ old('fat', $food->fat) }}" min="0">
                                            @error('fat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach
        <!-- Atribusi FatSecret -->
        <div class="col-12 mt-4">
            <footer>
                <p>Data nutrisi didukung oleh <a href="https://platform.fatsecret.com">FatSecret Platform API</a></p>
            </footer>
        </div>
    </div>

    <script>
        document.getElementById('searchBtn').addEventListener('click', function() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('searchResults').style.display = 'none';
        });
        window.addEventListener('load', function() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('searchResults').style.display = 'block';
        });
    </script>
@endsection