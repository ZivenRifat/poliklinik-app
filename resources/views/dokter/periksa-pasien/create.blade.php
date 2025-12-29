<x-layouts.app title="Periksa Pasien">
    {{-- ALERT FLASH MESSAGE --}}
    <div class="container-fluid px-4 mt-4">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h1 class="mb-4">Periksa Pasien</h1>

                @if (session('warning'))
                    <div class="alert alert-warning">
                        ⚠️ {{ session('warning') }}
                    </div>
                @endif


                @if (session('message'))
                    <div class="alert alert-{{ session('type', 'danger') }}">
                        {{ session('message') }}
                    </div>
                @endif


                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('periksa-pasien.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_daftar_poli" value="{{ $id }}">

                            {{-- Pilih Obat --}}
                            <div class="form-group mb-3">
                                <label for="obat" class="form-label">Pilih Obat</label>
                                <select id="select-obat" class="form-select">
                                    <option value="">-- Pilih Obat --</option>
                                    @foreach ($obats as $obat)
                                        <option value="{{ $obat->id }}" data-nama="{{ $obat->nama_obat }}"
                                            data-harga="{{ $obat->harga }}" data-stok="{{ $obat->stok }}"
                                            data-menipis="{{ $obat->stok <= 10 ? '1' : '0' }}">
                                            {{ $obat->nama_obat }}
                                            | Rp{{ number_format($obat->harga) }}
                                            | Stok: {{ $obat->stok }}
                                            @if ($obat->stok <= 10 && $obat->stok > 0)
                                                ⚠️ (Menipis)
                                            @endif
                                            @if ($obat->stok <= 0)
                                                ⚠️ (Habis)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Catatan -->
                            <div class="form-group mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control">{{ old('catatan') }}</textarea>
                            </div>

                            <!-- Obat Terpilih -->
                            <div class="form-group mb-3">
                                <label>Obat Terpilih</label>
                                <ul id="obat-terpilih" class="list-group mb-2"></ul>
                                <input type="hidden" name="biaya_periksa" id="biaya_periksa" value="0">
                                <input type="hidden" name="obat_json" id="obat_json">
                            </div>

                            <!-- Total Harga -->
                            <div class="form-group mb-3">
                                <label>Total Harga</label>
                                <p id="total-harga" class="fw-bold">Rp 0</p>
                            </div>

                            <!-- Tombol -->
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ route('periksa-pasien.index') }}" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>

<script>
    const selectObat = document.getElementById('select-obat');
    const listObat = document.getElementById('obat-terpilih');
    const inputBiaya = document.getElementById('biaya_periksa');
    const inputObatJson = document.getElementById('obat_json');
    const totalHargaEl = document.getElementById('total-harga');

    let daftarObat = [];

    // Event ketika memilih obat
    selectObat.addEventListener('change', () => {
        const option = selectObat.options[selectObat.selectedIndex];
        if (!option.value) return;

        const id = option.value;
        const nama = option.dataset.nama;
        const harga = parseInt(option.dataset.harga);
        const stok = parseInt(option.dataset.stok);
        const menipis = option.dataset.menipis === "1";

        if (daftarObat.some(o => o.id == id)) {
            alert('Obat sudah dipilih');
            return;
        }

        daftarObat.push({
            id,
            nama,
            harga
        });
        renderObat();
        selectObat.selectedIndex = 0;
    });


    // Render daftar obat
    function renderObat() {
        listObat.innerHTML = '';
        let total = 0;

        daftarObat.forEach((obat, index) => {
            total += obat.harga;

            const item = document.createElement('li');
            item.className = 'list-group-item d-flex justify-content-between align-items-center';
            item.innerHTML = `
                ${obat.nama} - Rp ${obat.harga.toLocaleString()}
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusObat(${index})">Hapus</button>
            `;
            listObat.appendChild(item);
        });

        inputBiaya.value = total;
        totalHargaEl.textContent = `Rp ${total.toLocaleString()}`;
        inputObatJson.value = JSON.stringify(daftarObat.map(o => o.id));
    }

    function hapusObat(index) {
        daftarObat.splice(index, 1);
        renderObat();
    }
</script>
