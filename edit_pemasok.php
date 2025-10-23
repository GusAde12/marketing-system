<script type="text/javascript">
	document.title = "Edit Pemasok";
	if (document.getElementById('pemasok')) {
		document.getElementById('pemasok').classList.add('active');
	}
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Edit Barang</h3>
				<?php
				if (!isset($_GET['id_pemasok'])) {
					die("ID Pemasok tidak ditemukan!");
				}
				$f = $root->edit_pemasok($_GET['id_pemasok']);
				?>
				<?php
if (!isset($_GET['id_pemasok'])) {
    die("Error: ID Pemasok tidak ditemukan!");
}

$f = $root->edit_pemasok($_GET['id_pemasok']);

?>

				<form class="form-input" method="post" action="handler.php?action=edit_pemasok" style="padding-top: 30px;">
					<input type="hidden" name="action" value="edit_pemasok">
					<input type="hidden" name="id_pemasok" value="<?= $f['id_pemasok'] ?>">
					

					<input type="text" placeholder="ID pemasok" disabled="disabled" value="ID pemasok: <?= $f['id_pemasok'] ?>">
					
					<label>Nama :</label>
					<input type="text" name="nama" placeholder="nama" required="required" value="<?= $f['nama'] ?>">

					<label>Alamat:</label>
					<input type="text" name="alamat" placeholder="alamat" required="required" value="<?= $f['alamat'] ?>">

					<label>Telp:</label>
					<input type="text" name="telp" placeholder="telp" required="required" value="<?= $f['telp'] ?>">

					<label>Penanggung Jawab:</label>
					<input type="text" name="penanggung_jawab" placeholder="penanggung jawab" required="required" value="<?= $f['penanggung_jawab'] ?>">
                    <label>Status:</label>
					<input type="text" name="status" placeholder="status" required="required" value="<?= $f['status'] ?>">

					<button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
					<a href="pemasok.php" class="btnblue" style="background: #f33155"><i class="fa fa-close"></i> Batal</a>
				</form>
			</div>
		</div>
	</div>
</div>
