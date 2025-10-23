<script type="text/javascript">
	document.title = "Tambah Produksi Barang";
	document.getElementById('barang_produksi').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Tambah Produksi Barang</h3>
				<form class="form-input" method="post" action="handler.php?action=tambah_produksi">
					
					<label>Tanggal Produksi</label>
					<input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>

					<label>Keterangan</label>
					<input type="text" name="keterangan" placeholder="Contoh: Produksi Rak dari Kayu Sengon" required>

					<label>Barang Jadi</label>
					<select id="barangJadiSelect" name="id_barang_jadi" style="width: 100%; cursor: pointer;">
						<option value="">-- Pilih Barang Jadi --</option>
						<option value="new">+ Tambah Barang Baru</option>
						<?php $root->tampil_barang_jadi_dropdown(); // hanya tipe_barang = 'jadi' ?>
					</select>

					<div id="barangBaruInput" style="margin-top: 10px; display: none;">
						<label>Nama Barang Jadi Baru</label>
						<input type="text" name="nama_barang_jadi" placeholder="Contoh: Rak Kayu 2 Susun">
					</div>


					<label>Jumlah Barang Jadi</label>
					<input type="number" name="jumlah_jadi" min="1" required>

					<hr style="margin: 20px 0;">

					<h4 style="margin-bottom: 10px;">Input Bahan Baku</h4>

					<div id="bahanContainer">
						<div class="bahan-item" style="margin-bottom: 15px;">
							<select name="id_barang_baku[]" required style="width: 100%; cursor: pointer;">
								<option value="">-- Pilih Bahan Baku --</option>
								<?php $root->tampil_barang2(); ?>
							</select>
							<input type="number" name="jumlah_baku[]" placeholder="Jumlah Digunakan" min="1" required style="margin-top: 8px;">
						</div>
					</div>

					<button type="button" onclick="tambahBahanBaku()" class="btnblue" style="margin-bottom: 20px;">
						<i class="fa fa-plus"></i> Tambah Bahan Baku
					</button>

					<br>
					<button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan Produksi</button>
					<a href="barang_produksi.php" class="btnblue" style="background: #f33155;"><i class="fa fa-close"></i> Batal</a>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
function tambahBahanBaku() {
	var container = document.getElementById('bahanContainer');
	var div = document.createElement('div');
	div.className = 'bahan-item';
	div.style.marginBottom = '15px';
	div.innerHTML = `
		<select name="id_barang_baku[]" required style="width: 100%; cursor: pointer;">
			<option value="">-- Pilih Bahan Baku --</option>
			<?php ob_start(); $root->tampil_barang2(); $options = ob_get_clean(); echo addslashes($options); ?>
		</select>
		<input type="number" name="jumlah_baku[]" placeholder="Jumlah Digunakan" min="1" required style="margin-top: 8px;">
	`;
	container.appendChild(div);
}
</script>

<script>
document.getElementById('barangJadiSelect').addEventListener('change', function () {
	var value = this.value;
	var inputBaru = document.getElementById('barangBaruInput');
	
	if (value === 'new') {
		inputBaru.style.display = 'block';
	} else {
		inputBaru.style.display = 'none';
	}
});
</script>

