<script type="text/javascript">
    document.title = "Edit Stok";
    if (document.getElementById('stok')) {
        document.getElementById('stok').classList.add('active');
    }
</script>

<div class="content">
    <div class="padding">
        <div class="bgwhite">
            <div class="padding">
                <h3 class="jdl">Edit Stok Opname</h3>
                <?php
                if (!isset($_GET['id_opname'])) {
                    die("Error: ID Stok tidak ditemukan!");
                }
                $f = $root->edit_stok($_GET['id_opname']);
                ?>

                <form class="form-input" method="post" action="handler.php?action=edit_stok" style="padding-top: 30px;">
                    <input type="hidden" name="action" value="edit_stok">
                    <input type="hidden" name="id_opname" value="<?= $f['id_opname'] ?>">
                    <input type="hidden" name="id_barang" value="<?= $f['id_barang'] ?>">

                    <input type="text" placeholder="ID Stok Opname" disabled="disabled" value="ID: <?= $f['id_opname'] ?>">

                    <label>Stok Barang:</label>
                    <input type="number" name="stok_barang" id="stok_barang" placeholder="Stok Barang" required="required" value="<?= $f['stok_barang'] ?>" readonly>

                    <label>Jumlah Asli:</label>
                    <input type="number" name="jumlah_asli" id="jumlah_asli" placeholder="Jumlah Asli" required="required" value="<?= $f['jumlah_asli'] ?>">

                    <label>Selisih:</label>
                    <input type="number" name="selisih" id="selisih" placeholder="Selisih" required="required" value="<?= $f['selisih'] ?>" readonly>

                    <label>Tanggal Opname:</label>
                    <input type="date" name="tanggal" id="tanggal" required="required" value="<?= $f['tanggal'] ?>" readonly>

                    <label>Keterangan:</label>
                    <input type="text" name="keterangan" placeholder="Keterangan" required="required" value="<?= $f['keterangan'] ?>">

                    <button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
                    <a href="stok.php" class="btnblue" style="background: #f33155"><i class="fa fa-close"></i> Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stokInput = document.getElementById('stok_barang');
    const jumlahAsliInput = document.getElementById('jumlah_asli');
    const selisihInput = document.getElementById('selisih');

    function hitungSelisih() {
        const stok = parseInt(stokInput.value) || 0;
        const jumlahAsli = parseInt(jumlahAsliInput.value) || 0;
        selisihInput.value = jumlahAsli - stok;
    }

    // Hitung ulang saat pengguna mengubah jumlah asli
    jumlahAsliInput.addEventListener('input', hitungSelisih);

    // Hitung selisih awal saat halaman dibuka
    hitungSelisih();
});
</script>
