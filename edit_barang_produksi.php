<script type="text/javascript">
	document.title = "Edit Harga Jual Barang Produksi";
	document.getElementById('barang_produksi').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Edit Harga Jual Barang Produksi</h3>
				<?php
				$f = $root->edit_barang_p($_GET['id_barang']);
				if (!$f) {
					echo "<p>Data barang tidak ditemukan.</p>";
					exit;
				}
				?>
				<form class="form-input" method="post" action="handler.php?action=edit_harga_jual" style="padding-top: 30px;">
					<input type="hidden" name="id_barang" value="<?= $f['id_barang'] ?>">
					
					<input type="text" disabled value="ID Barang: <?= $f['id_barang'] ?>">
					
					<label>Nama Barang:</label>
					<input type="text" disabled value="<?= $f['nama_barang'] ?>">

					<label>Harga Jual:</label>
					<input type="number" name="harga_jual" placeholder="Harga Jual" required value="<?= $f['harga_jual'] ?>">

					<br><br>
					<button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
					<a href="barang_produksi.php" class="btnblue" style="background: #f33155"><i class="fa fa-close"></i> Batal</a>
				</form>
			</div>
		</div>
	</div>
</div>
