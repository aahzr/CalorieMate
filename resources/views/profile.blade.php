@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="fs-4 fw-semibold text-primary">Profil Anda</h1>
                    <p class="fs-6 text-secondary">Kelola foto profil Anda.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="fs-5 fw-semibold text-primary mb-4">Foto Profil</h2>
                    <div class="text-center mb-4">
                        <img src="{{ Auth::user()->profile_photo ? Storage::url(Auth::user()->profile_photo) : 'https://storage.googleapis.com/a1aa/image/6925a0fb-4828-4965-788a-92ffb475c3cc.jpg' }}" alt="Profile picture" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('profile.updatePhoto') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Pilih Foto</label>
                            <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*" required>
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Unggah Foto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection