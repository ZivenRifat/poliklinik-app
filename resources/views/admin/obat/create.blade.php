<x-layouts.app title="Tambah Obat">
    <div class="container-fluid px-4 mt-4">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h1 class="mb-4">Tambah Obat</h1>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('obat.store') }}" method="POST">
                            @csrf

                            {{-- NAMA OBAT --}}
                            <div class="form-group mb-3">
                                <label>Nama Obat <span class="text-danger">*</span></label>
                                <input type="text" name="nama_obat"
                                    class="form-control @error('nama_obat') is-invalid @enderror"
                                    value="{{ old('nama_obat') }}" required>
                                @error('nama_obat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- KEMASAN --}}
                            <div class="form-group mb-3">
                                <label>Kemasan <span class="text-danger">*</span></label>
                                <input type="text" name="kemasan"
                                    class="form-control @error('kemasan') is-invalid @enderror"
                                    value="{{ old('kemasan') }}" required>
                                @error('kemasan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- HARGA --}}
                            <div class="form-group mb-3">
                                <label>Harga <span class="text-danger">*</span></label>
                                <input type="number" name="harga"
                                    class="form-control @error('harga') is-invalid @enderror"
                                    value="{{ old('harga') }}" min="0" required>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- STOK --}}
                            <div class="form-group mb-3">
                                <label>Stok <span class="text-danger">*</span></label>
                                <input type="number" name="stok"
                                    class="form-control @error('stok') is-invalid @enderror"
                                    value="{{ old('stok', 0) }}" min="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button class="btn btn-success">Simpan</button>
                            <a href="{{ route('obat.index') }}" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
