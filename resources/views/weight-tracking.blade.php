@extends('layouts.app')

@section('title', 'Weight Tracking')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex justify-content-end align-items-center gap-3 mb-4">
                <i class="fas fa-calendar-alt text-primary" id="calendarIcon" style="cursor: pointer;"></i>
                <a href="{{ route('weight-tracking', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" aria-label="Previous day" class="btn btn-link text-primary"><i class="fas fa-chevron-left"></i></a>
                <span class="fs-6 fw-semibold text-primary">{{ $date->format('D, d M Y') }}</span>
                <a href="{{ route('weight-tracking', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" aria-label="Next day" class="btn btn-link text-primary"><i class="fas fa-chevron-right"></i></a>
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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-bullseye text-primary fs-5"></i>
                            <span class="fs-5 fw-semibold text-primary">Target Berat</span>
                        </div>
                        <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#setTargetWeightModal"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="fs-2 fw-bold text-primary">{{ $targetWeight ?? 'N/A' }}</span>
                        <span class="fs-5 text-primary">kg</span>
                    </div>
                    @if($targetWeight && $currentWeight && isset($calorieGoalType))
                        <div class="d-flex align-items-center gap-2 mt-3">
                            <span class="badge bg-primary text-white">{{ $progress }}% progres</span>
                            <div class="progress flex-grow-1">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="fs-6 text-secondary">
                                @if($calorieGoalType == 'deficit')
                                    {{ $currentWeight->weight > $targetWeight ? ($currentWeight->weight - $targetWeight) . ' kg lagi (Defisit)' : 'Sudah tercapai!' }}
                                @else
                                    {{ $currentWeight->weight < $targetWeight ? ($targetWeight - $currentWeight->weight) . ' kg lagi (Surplus)' : 'Sudah tercapai!' }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12">
            <form action="{{ route('weight-tracking.store', ['date' => $date->format('Y-m-d')]) }}" method="POST" class="card mb-4">
                @csrf
                <div class="card-body">
                    <h3 class="fs-5 fw-semibold text-primary mb-3">Tambah Berat</h3>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror" placeholder="Berat (kg)" step="0.1" min="0" value="{{ old('weight') }}" required>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
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
                                <div class="d-flex gap-2">
                                    <span class="text-secondary">{{ $log->formatted_date }}</span>
                                    <form action="{{ route('weight-tracking.destroy', ['id' => $log->id, 'date' => $date->format('Y-m-d')]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Hapus log ini?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
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

    <!-- Modal Set Target Weight -->
    <div class="modal fade" id="setTargetWeightModal" tabindex="-1" aria-labelledby="setTargetWeightModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setTargetWeightModalLabel">Atur Target Berat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('weight-tracking.set-target', ['date' => $date->format('Y-m-d')]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="target_weight">Target Berat (kg)</label>
                            <input type="number" class="form-control @error('target_weight') is-invalid @enderror" id="target_weight" name="target_weight" value="{{ old('target_weight', $targetWeight) }}" step="0.1" min="0" required>
                            @error('target_weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="calorie_goal_type">Tujuan Kalori</label>
                            <select name="calorie_goal_type" id="calorie_goal_type" class="form-control @error('calorie_goal_type') is-invalid @enderror" required>
                                <option value="deficit" {{ old('calorie_goal_type', $calorieGoalType ?? 'deficit') == 'deficit' ? 'selected' : '' }}>Defisit (Turun Berat)</option>
                                <option value="surplus" {{ old('calorie_goal_type', $calorieGoalType ?? 'deficit') == 'surplus' ? 'selected' : '' }}>Surplus (Naik Berat)</option>
                            </select>
                            @error('calorie_goal_type')
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

    <script>
        const calendarIcon = document.getElementById('calendarIcon');
        const datePicker = document.createElement('input');
        datePicker.type = 'date';
        datePicker.id = 'customDatePicker';
        datePicker.style.position = 'absolute';
        datePicker.style.zIndex = '1000';
        datePicker.style.border = '1px solid #ced4da';
        datePicker.style.borderRadius = '0.25rem';
        datePicker.style.padding = '0.375rem 0.75rem';
        datePicker.style.fontSize = '0.875rem';
        datePicker.style.backgroundColor = '#fff';
        datePicker.style.display = 'none';
        document.body.appendChild(datePicker);

        calendarIcon.addEventListener('click', function(e) {
            e.preventDefault();
            const iconRect = calendarIcon.getBoundingClientRect();
            datePicker.style.top = (iconRect.bottom + window.scrollY) + 'px';
            datePicker.style.left = (iconRect.left + window.scrollX) + 'px';
            datePicker.value = '{{ $date->format('Y-m-d') }}';
            datePicker.style.display = 'block';
            datePicker.focus();

            datePicker.onchange = function() {
                const selectedDate = this.value;
                if (selectedDate) {
                    window.location.href = "{{ route('weight-tracking') }}?date=" + selectedDate;
                    datePicker.style.display = 'none';
                }
            };

            document.addEventListener('click', function hidePicker(event) {
                if (!calendarIcon.contains(event.target) && !datePicker.contains(event.target)) {
                    datePicker.style.display = 'none';
                    document.removeEventListener('click', hidePicker);
                }
            });
        });
    </script>

    @section('styles')
        <style>
            #calendarIcon {
                font-size: 1.2rem;
                cursor: pointer;
            }
            #customDatePicker {
                cursor: pointer;
            }
            #customDatePicker:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
                outline: none;
            }
        </style>
    @endsection
@endsection