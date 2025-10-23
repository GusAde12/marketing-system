<script type="text/javascript">
    document.title = "Tambah Stok";
    document.getElementById('stok').classList.add('active');
</script>

<div class="content">
    <div class="padding">
        <div class="bgwhite">
            <div class="padding">
                <h2>Form Input Stok Opname</h2>
                <form class="form-input" method="post" action="handler.php?action=tambah_stok">
                    <label for="id_barang">Nama Barang</label>
                    <select id="id_barang" name="id_barang" required class="form-control">
                        <option value="">-- Pilih Barang --</option>
                        <?php
                        include 'koneksi.php';
                        $query = $conn->query("SELECT id_barang, nama_barang, stok FROM barang ORDER BY nama_barang ASC");
                        while ($row = $query->fetch_assoc()) {
                            echo "<option value='" . $row['id_barang'] . "' data-stok='" . $row['stok'] . "'>" . $row['id_barang'] . " - " . $row['nama_barang'] . "</option>";
                        }
                        ?>

                    </select>

                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required class="form-control">

                    <label for="stok_barang">Stok Barang</label>
                    <input type="number" id="stok_barang" name="stok_barang" placeholder="Stok Barang" required class="form-control">

                    <label for="jumlah_asli">Jumlah Asli</label>
                    <input type="number" id="jumlah_asli" name="jumlah_asli" placeholder="Jumlah Asli" required class="form-control">

                    <label for="selisih">Selisih</label>
                    <input type="number" id="selisih" name="selisih" placeholder="Selisih" required class="form-control">

                    <label for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" placeholder="Keterangan" required class="form-control">

                    <button class="btnblue" type="submit">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="stok.php" class="btnblue" style="background: #f33155">
                        <i class="fa fa-close"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Isi otomatis tanggal hari ini
    const tanggalInput = document.getElementById('tanggal');
    const today = new Date().toISOString().split('T')[0];
    tanggalInput.value = today;
    tanggalInput.readOnly = true; // Biar tidak bisa diubah manual

    // Auto isi stok_barang saat memilih barang
    const selectBarang = document.getElementById('id_barang');
    const stokInput = document.getElementById('stok_barang');

    selectBarang.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const stok = selectedOption.getAttribute('data-stok');
        stokInput.value = stok ? stok : '';
    });

    // Opsional: Hitung selisih otomatis
    const jumlahAsliInput = document.getElementById('jumlah_asli');
    const selisihInput = document.getElementById('selisih');

    function hitungSelisih() {
        const stok = parseInt(stokInput.value) || 0;
        const jumlahAsli = parseInt(jumlahAsliInput.value) || 0;
        selisihInput.value = jumlahAsli - stok;
    }

    jumlahAsliInput.addEventListener('input', hitungSelisih);
    stokInput.addEventListener('input', hitungSelisih);
});
</script>
