@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
    <h1>Tambah Buku</h1>
    <p><a href="{{ route('books.index') }}">&larr; Kembali ke daftar buku</a></p>

    <form action="{{ route('books.store') }}" method="POST">
        @csrf

        <label for="judul">Judul</label>
        <input type="text" name="judul" id="judul" value="{{ old('judul') }}">
        @error('judul')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <label for="penulis">Penulis</label>
        <input type="text" name="penulis" id="penulis" value="{{ old('penulis') }}">
        @error('penulis')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <label for="penerbit">Penerbit</label>
        <input type="text" name="penerbit" id="penerbit" value="{{ old('penerbit') }}">
        @error('penerbit')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <label for="tahun_terbit">Tahun Terbit</label>
        <input type="number" name="tahun_terbit" id="tahun_terbit" value="{{ old('tahun_terbit') }}">
        @error('tahun_terbit')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <label for="isbn">ISBN (opsional)</label>
        <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}">
        @error('isbn')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <label for="stok">Stok</label>
        <input type="number" name="stok" id="stok" value="{{ old('stok', 1) }}">
        @error('stok')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <label for="category_id">Kategori</label>
        <select name="category_id" id="category_id" style="width: 100%; padding: 6px; margin-top: 4px;">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category['id'] }}" @selected(old('category_id') == $category['id'])>
                    {{ $category['nama_kategori'] }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="error" style="color: #b91c1c; font-size: 14px; margin-top: 4px;">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn" style="margin-top: 20px;">Simpan</button>
    </form>
@endsection