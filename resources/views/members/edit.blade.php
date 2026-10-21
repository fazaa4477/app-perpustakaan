<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Anggota</title>
    <style>
        body { font-family: sans-serif; margin: 40px; max-width: 500px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 6px; margin-top: 4px; box-sizing: border-box; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 4px; }
        .btn { margin-top: 20px; padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Edit Anggota</h1>
    <p><a href="{{ route('members.index') }}">&larr; Kembali ke daftar anggota</a></p>

    <form action="{{ route('members.update', $member['id']) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="nama">Nama</label>
        <input type="text" name="nama" id="nama" value="{{ old('nama', $member['nama']) }}">
        @error('nama')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="nim">NIM</label>
        <input type="text" name="nim" id="nim" value="{{ old('nim', $member['nim']) }}">
        @error('nim')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $member['email']) }}">
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="nomor_telepon">Nomor Telepon</label>
        <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon', $member['nomor_telepon']) }}">
        @error('nomor_telepon')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="alamat">Alamat</label>
        <textarea name="alamat" id="alamat" rows="3">{{ old('alamat', $member['alamat']) }}</textarea>
        @error('alamat')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="aktif" @selected(old('status', $member['status']) == 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(old('status', $member['status']) == 'nonaktif')>Nonaktif</option>
        </select>
        @error('status')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn">Perbarui</button>
    </form>
</body>
</html>