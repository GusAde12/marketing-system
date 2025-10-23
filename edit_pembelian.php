<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Kode lainnya
?>

<script type="text/javascript">
    document.title = "Edit Pembelian";
    document.getElementById('pembelian').classList.add('active');
</script>

<div class="content">
    <div class="padding">
        <div class="bgwhite">
            <div class="padding">
                <h3 class="jdl">Edit Pembelian</h3>
                <?php $f=$root->edit_pembelian($_GET['id_pembelian']) ?>
                <form class="form-input" method="post" action="handler.php?action=edit_pembelian">
                    <!-- Input ID Pembelian yang disembunyikan -->
                    <input type="hidden" name="id_pembelian" value="<?= $f['id_pembelian'] ?>">

                    <!-- No Faktur Pembelian yang tidak dapat diubah -->
                    <input type="text" id="no_faktur_pembelian" name="no_faktur_pembelian" placeholder="No Faktur Pembelian" required readonly value="<?= $f['no_faktur_pembelian'] ?>">

                    <!-- Dropdown Distributor -->
                    <select id="iddistributor" name="distributor" required>
                        <option value="">Pilih Distributor:</option>
                        <?php $root->tampil_distributor2($f['distributor']); ?>
                        <option value="penjual lain" <?= ($f['distributor'] == 'penjual lain') ? 'selected' : '' ?>>Penjual Lain</option>
                    </select>


                    <!-- Nama Penjual dan Barang Lain (Hanya muncul jika penjual lain) -->
                    <input type="text" id="nama_penjual" name="nama" placeholder="Nama Penjual" value="<?= $f['nama'] ?>" <?= $f['distributor'] == 'penjual lain' ? '' : 'disabled' ?>>
                    <input type="text" id="nama_barang_lain" name="barang_lain" placeholder="Barang Lain" value="<?= $f['barang_lain'] ?>" <?= $f['distributor'] == 'penjual lain' ? '' : 'disabled' ?>>

                    <!-- Dropdown Barang -->
                    <select id="idbarang" name="barang">
                        <option value="">Pilih Barang:</option>
                        <?php $root->tampil_barang2($f['id_barang']); ?>
                    </select>

                    <!-- Input Jumlah -->
                    <input type="number" id="jumlah" name="jumlah" placeholder="Jumlah" required min="1" step="1" value="<?= $f['jumlah'] ?>">

                    <!-- Input Harga Satuan -->
                    <input type="number" id="harga" name="harga" placeholder="Harga Satuan" required min="0" step="0.01" value="<?= $f['harga'] ?>">

                    <!-- Input Total (Read-only) -->
                    <input type="number" id="total" name="total" placeholder="Total" required readonly value="<?= $f['total'] ?>">

                    <!-- Input Tanggal -->
                    <input type="date" name="tanggal" required value="<?= $f['tanggal'] ?>">

                    <!-- Tombol Simpan dan Batal -->
                    <button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
                    <a href="pembelian.php" class="btnblue" style="background: #f33155"><i class="fa fa-close"></i> Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const distributorSelect = document.getElementById("iddistributor");
    const namaPenjualInput = document.getElementById("nama_penjual");
    const namaBaranglainInput = document.getElementById("nama_barang_lain");
    const barangSelect = document.getElementById("idbarang");
    const jumlahInput = document.getElementById("jumlah");
    const hargaInput = document.getElementById("harga");
    const totalInput = document.getElementById("total");
    const form = document.querySelector(".form-input");

    // Fungsi hitung total
    function hitungTotal() {
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;
        totalInput.value = (jumlah * harga).toFixed(2);
    }

    jumlahInput.addEventListener("input", hitungTotal);
    hargaInput.addEventListener("input", hitungTotal);

    // Event listener perubahan distributor
    distributorSelect.addEventListener("change", async function () {
        const selectedOption = this.options[this.selectedIndex];
        const idDistributor = selectedOption.getAttribute('data-id') || "";
        //const idBarang = selectedOption.getAttribute('data-idbarang') || "";

        if (this.value === "penjual lain") {
            namaPenjualInput.removeAttribute("disabled");
            namaBaranglainInput.removeAttribute("disabled");
            //barangSelect.setAttribute("disabled", "true");
            barangSelect.disabled = true;
            barangSelect.value = "";
            barangSelect.required = false;
        } else {
            namaPenjualInput.setAttribute("disabled", "true");
            namaBaranglainInput.setAttribute("disabled", "true");
            namaPenjualInput.value = "";
            namaBaranglainInput.value = "";
            barangSelect.removeAttribute("disabled");
            barangSelect.required = true;

            // Ambil daftar barang berdasarkan distributor
            if (idDistributor) {
                barangSelect.innerHTML = '<option value="">Memuat...</option>';
                try {
                    const response = await fetch(`handler.php?action=get_barang_by_distributor&id_distributor=${idDistributor}`);
                    const data = await response.json();
                    barangSelect.innerHTML = '<option value="">Pilih Barang :</option>';
                    data.forEach(barang => {
                        let option = document.createElement('option');
                        option.value = barang.id_barang;
                        option.textContent = barang.nama_barang;
                        barangSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error fetching barang:', error);
                    barangSelect.innerHTML = '<option value="">Gagal memuat barang</option>';
                }
            } else {
                barangSelect.innerHTML = '<option value="">Pilih Barang :</option>';
            }
        }
    });

    // Event listener perubahan barang untuk mendapatkan harga
        barangSelect.addEventListener("change", async function() {
        if (this.value) {
            try {
                const response = await fetch(`handler.php?action=get_harga_barang&id_barang=${this.value}`);
                const data = await response.json();

                // Ambil harga yang sekarang sudah ada di input
                const currentHarga = parseFloat(hargaInput.value);

                // Jika harga masih kosong atau 0, baru isi dengan harga dari server
                if (!currentHarga || currentHarga === 0) {
                    hargaInput.value = data.harga || 0;
                    hitungTotal();
                }
                // Kalau harga sudah terisi (misalnya 100000), jangan ubah
            } catch (error) {
                console.error('Error fetching harga:', error);
            }
        }
    });


    // Generate no faktur saat halaman dimuat
    async function generateNoFaktur() {
        try {
            const response = await fetch('handler.php?action=get_no_faktur');
            const data = await response.text();
            document.getElementById('no_faktur_pembelian').value = data;
        } catch (error) {
            console.error('Error fetching no faktur:', error);
        }
    }
    generateNoFaktur();

    // Validasi sebelum submit
    form.addEventListener("submit", function (event) {
        if (distributorSelect.value === "penjual lain") {
            barangSelect.value = "";
        }
    });
});
</script>
