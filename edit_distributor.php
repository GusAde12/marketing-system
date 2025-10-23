<script type="text/javascript">
	document.title="Edit Distributor";
	document.getElementById('distributor').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Edit Distributor</h3>
				<?php
				$f=$root->edit_distributor($_GET['id_distributor']);
				?>
				<form class="form-input" method="post" action="handler.php?action=edit_distributor" style="padding-top: 30px;">	<input type="hidden" name="id_distributor" value="<?= $f['id_distributor'] ?>">
					<input type="text" placeholder="ID Kategori" disabled="disabled" value="ID barang : <?= $f['id_distributor'] ?>">
					<label>Nama Pemasok :</label>
					<input type="text" name="nama_pemasok" placeholder="Nama Pemasok" required="required" value="<?= $f['nama_pemasok'] ?>">
					<label>Alamat :</label>
					<input type="text" name="alamat" placeholder="Alamat" required="required" value="<?= $f['alamat'] ?>">
					<label>Telp :</label>
					<input type="number" name="telp" placeholder="Telp" required="required"value="<?= $f['telp'] ?>">
					<label>Nama Penanggung Jawab :</label>
					<input type="text" name="nama_penanggung_jawab" placeholder="Nama Penanggung Jawab" required="required" value="<?= $f['nama_penanggung_jawab'] ?>">
					<!-- <label>Status :</label>
					<input type="text" name="status" placeholder="Status" required="required" value="<?= $f['status'] ?>">
					 -->
					<!-- <label>Kategori :</label>
					<select style="width: 372px;cursor: pointer;" required="required" name="kategori">
						<option value="">Pilih Kategori :</option>
						<?php $root->tampil_kategori3($_GET['id_barang']); ?>
					</select> -->
					<button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
					<a href="distributor.php" class="btnblue" style="background: #f33155"><i class="fa fa-close"></i> Batal</a>
				</form>
			</div>
		</div>
	</div>
</div>
