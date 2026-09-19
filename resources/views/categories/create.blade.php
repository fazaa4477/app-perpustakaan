@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <h1>Tambah Kategori</h1>
    <p><a href="{{ route('categories.index') }}">&larr; Kembali ke daftar kategori</a></p>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <label for="nama_kategori">Nama Kategori</label>
        <input type="text" name="nama_kategori" id="nama_kategori" value="{{ old('nama_kategori') }}" style="width: 100%; padding: 6px; margin-top: 4px; box-sizing: border-box;">
        @error('nama_kategori')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <label for="deskripsi">Deskripsi (opsional)</label>
        <textarea name="deskripsi" id="deskripsi" rows="4" style="width: 100%; padding: 6px; margin-top: 4px; box-sizing: border-box;">{{ old('deskripsi') }}</textarea>
        @error('deskripsi')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn" style="margin-top: 20px;">Simpan</button>
    </form>
@endsection