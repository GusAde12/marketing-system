<script type="text/javascript">
    document.title = "Tambah Pembelian";
    document.getElementById('pembelian').classList.add('active');
</script>

<div class="content">
    <div class="padding">
        <div class="bgwhite">
            <div class="padding">
                <h3 class="jdl">Tambah Pembelian</h3>
                <form class="form-input" method="post" action="handler.php?action=tambah_pembelian">
                    <input type="text" id="no_faktur_pembelian" name="no_faktur_pembelian" placeholder="No Faktur Pembelian" required readonly>

                    <select id="iddistributor" name="distributor" required>
                        <option value="">Pilih Distributor:</option>
                        <?php $root->tampil_distributor2(); ?>
                        <option value="penjual lain">Penjual Lain</option>
                    </select>

                    <input type="text" id="nama_penjual" name="nama" placeholder="Nama Penjual" disabled>
                    <!-- <input type="text" id="nama_barang_lain" name="barang_lain" placeholder="Barang Lain" disabled> -->

                    <h4>Daftar Barang yang Dibeli:</h4>
                    <div id="list-barang">
                        <div class="barang-item">
                            <!-- Dropdown untuk barang dari distributor -->
                            <select class="barang-dropdown" name="barang[]">
                                <option value="">Pilih Barang:</option>
                                <?php $root->tampil_barang2(); ?>
                            </select>

                            <!-- Input manual nama barang lain -->
                            <input type="text" name="barang_lain[]" class="barang-lain" placeholder="Nama Barang (Penjual Lain)" disabled>

                            <input type="number" name="jumlah[]" class="jumlah" placeholder="Jumlah" required min="1">
                            <input type="number" name="harga[]" class="harga" placeholder="Harga Satuan" required min="0" step="0.01">
                            <input type="number" name="total[]" class="total" placeholder="Total" readonly>
                            <button type="button" class="btnhapus" onclick="hapusBarang(this)">Hapus</button>
                        </div>
                    </div>


                    <button type="button" class="btnblue" onclick="tambahBarang()">+ Tambah Barang</button>

                    <br><br>
                    <label><strong>Total Keseluruhan Pembelian:</strong></label>
                    <input type="number" id="total_semua" name="total_semua" placeholder="Total Keseluruhan" readonly style="font-weight:bold; background: #f0f0f0">

                    <br><br>
                    <input type="date" name="tanggal" id="tanggal" required>

                    <br><br>
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
    const tanggalInput = document.getElementById("tanggal");
    const totalSemuaInput = document.getElementById("total_semua");
    const form = document.querySelector(".form-input");

    const today = new Date().toISOString().split('T')[0];
    tanggalInput.value = today;
    tanggalInput.readOnly = true;

    window.tambahBarang = function () {
    const item = document.querySelector('.barang-item').cloneNode(true);

    // Reset nilai input
    item.querySelectorAll('input').forEach(input => input.value = '');

    // Salin dropdown barang dari yang pertama
    const select = item.querySelector('.barang-dropdown');
    select.innerHTML = document.querySelector('.barang-dropdown').innerHTML;

    // Tambahkan ke list
    document.getElementById('list-barang').appendChild(item);

    // Aktifkan kembali logika disable/enable berdasarkan distributor saat ini
    toggleBarangInputByDistributor(item);

    applyListeners(item);
}

    function toggleBarangInputByDistributor(barangItem) {
    const isPenjualLain = distributorSelect.value === "penjual lain";
    const dropdown = barangItem.querySelector('.barang-dropdown');
    const barangLainInput = barangItem.querySelector('.barang-lain');

    if (isPenjualLain) {
        dropdown.setAttribute("disabled", true);
        dropdown.removeAttribute("name"); // Jangan ikut dikirim
        barangLainInput.removeAttribute("disabled");
        barangLainInput.setAttribute("name", "barang_lain[]");
    } else {
        dropdown.removeAttribute("disabled");
        dropdown.setAttribute("name", "barang[]");
        barangLainInput.setAttribute("disabled", true);
        barangLainInput.removeAttribute("name"); // Jangan ikut dikirim
    }
}



    window.hapusBarang = function (btn) {
        const items = document.querySelectorAll('.barang-item');
        if (items.length > 1) {
            btn.parentElement.remove();
            hitungTotalSemua();
        }
    }

    function hitungTotalPerBaris(baris) {
        const jumlah = parseFloat(baris.querySelector('.jumlah').value) || 0;
        const harga = parseFloat(baris.querySelector('.harga').value) || 0;
        const total = jumlah * harga;
        baris.querySelector('.total').value = total.toFixed(2);
        hitungTotalSemua();
    }

    function hitungTotalSemua() {
        const totalFields = document.querySelectorAll(".total");
        let totalKeseluruhan = 0;
        totalFields.forEach(field => {
            totalKeseluruhan += parseFloat(field.value) || 0;
        });
        totalSemuaInput.value = totalKeseluruhan.toFixed(2);
    }

    function applyListeners(baris) {
        baris.querySelector('.jumlah').addEventListener("input", () => hitungTotalPerBaris(baris));
        baris.querySelector('.harga').addEventListener("input", () => hitungTotalPerBaris(baris));
    }

    document.querySelectorAll('.barang-item').forEach(applyListeners);

    distributorSelect.addEventListener("change", async function () {
    const isPenjualLain = this.value === "penjual lain";

    // 👉 Perbaikan ini penting:
    if (isPenjualLain) {
        namaPenjualInput.removeAttribute("disabled");
    } else {
        namaPenjualInput.setAttribute("disabled", true);
        namaPenjualInput.value = "";
    }

    document.querySelectorAll(".barang-item").forEach(item => {
        toggleBarangInputByDistributor(item);
    });

    if (!isPenjualLain) {
        // Proses AJAX untuk muat barang tetap seperti sebelumnya
        const idDistributor = this.options[this.selectedIndex].getAttribute('data-id') || "";
        document.querySelectorAll(".barang-dropdown").forEach(async select => {
            select.innerHTML = '<option value="">Memuat...</option>';
            try {
                const response = await fetch(`handler.php?action=get_barang_by_distributor&id_distributor=${idDistributor}`);
                const data = await response.json();
                select.innerHTML = '<option value="">Pilih Barang :</option>';
                data.forEach(barang => {
                    let option = document.createElement('option');
                    option.value = barang.id_barang;
                    option.textContent = barang.nama_barang;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Gagal memuat barang:', error);
                select.innerHTML = '<option value="">Gagal memuat barang</option>';
            }
        });
    }
});


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

    form.addEventListener("submit", function () {
        if (distributorSelect.value === "penjual lain") {
            document.querySelectorAll(".barang-dropdown").forEach(el => el.remove());
        }
    });
});
</script>

<style>
    .btnhapus {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 6px 10px;
        margin-left: 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .btnhapus:hover {
        background-color: #c82333;
    }

    .barang-item {
        margin-bottom: 10px;
    }

    .barang-item input, .barang-item select {
        margin-right: 8px;
    }

    #total_semua {
        width: 200px;
        font-size: 1.1em;
        padding: 6px;
        color: #222;
    }
</style>
