@extends('layouts.app')

@section('title', 'Diet Journal')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    @auth
                        <h1 class="fs-4 fw-semibold text-primary">Jurnal Diet, {{ Auth::user()->name }}</h1>
                    @else
                        <h1 class="fs-4 fw-semibold text-primary">Jurnal Diet</h1>
                    @endauth
                    <p class="fs-6 text-secondary">Catat pengalaman diet harian Anda.</p>
                </div>
                @auth
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJournalModal">
                        <i class="fas fa-plus me-1"></i> Tambah Catatan
                    </button>
                @endauth
            </div>
        </div>
        @auth
            <!-- Konten jurnal -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if ($journals->isEmpty())
                            <p class="text-secondary">Belum ada catatan jurnal. Tambahkan sekarang!</p>
                        @else
                            @foreach ($journals as $journal)
                                <div class="border-bottom py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="fw-semibold text-primary">{{ $journal->created_at->format('d M Y, H:i') }}</p>
                                            <p class="text-secondary">{{ $journal->content }}</p>
                                            <p class="text-secondary">Mood: 
                                                <span class="fw-semibold">
                                                    @if($journal->mood == 'Happy')
                                                        😊 Happy
                                                    @elseif($journal->mood == 'Neutral')
                                                        😐 Neutral
                                                    @else
                                                        😢 Sad
                                                    @endif
                                                </span>
                                            </p>
                                        </div>
                                        <form action="{{ route('diet-journal.destroy', $journal->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Hapus jurnal ini?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <!-- Modal untuk tambah jurnal -->
            <div class="modal fade" id="addJournalModal" tabindex="-1" aria-labelledby="addJournalModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-primary" id="addJournalModalLabel">Tambah Catatan Jurnal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('diet-journal.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="content" class="form-label">Catatan</label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="4" required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="mood" class="form-label">Mood</label>
                                    <select class="form-control @error('mood') is-invalid @enderror" id="mood" name="mood" required>
                                        <option value="Happy" {{ old('mood') == 'Happy' ? 'selected' : '' }}>😊 Happy</option>
                                        <option value="Neutral" {{ old('mood') == 'Neutral' ? 'selected' : '' }}>😐 Neutral</option>
                                        <option value="Sad" {{ old('mood') == 'Sad' ? 'selected' : '' }}>😢 Sad</option>
                                    </select>
                                    @error('mood')
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
        @endauth
    </div>
@endsection