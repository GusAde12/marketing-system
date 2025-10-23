<script type="text/javascript">
	document.title="Tambah Distributor";
	document.getElementById('distributor').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Tambah Distributor</h3>
				<form class="form-input" method="post" action="handler.php?action=tambah_distributor">
					<input type="text" name="nama_pemasok" placeholder="Nama Pemasok" required="required">
					<input type="text" name="alamat" placeholder="Alamat" required="required">
					<input type="number" name="telp" placeholder="Telp" required="required">
					<input type="text" name="nama_penanggung_jawab" placeholder="Nama Penanggung Jawab" required="required">
					<!-- <input type="text" name="status" placeholder="Status" required="required"> -->
					<!-- <select style="width: 372px;cursor: pointer;" required="required" name="kategori">
						<option value="">Pilih Kategori :</option>
						<?php $root->tampil_kategori2(); ?>
					</select> -->
					<button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
					<a href="distributor.php" class="btnblue" style="background: #f33155"><i class="fa fa-close"></i> Batal</a>
				</form>
			</div>
		</div>
	</div>
</div>
