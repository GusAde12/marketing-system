<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<script type="text/javascript">
	document.title = "Edit User";
	document.getElementById('users').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Edit User</h3>
				<form class="form-input" method="post" action="handler.php?action=edit_user">
					<?php $f = $root->edit_user($_GET['id']); ?>
					<input type="hidden" name="id" value="<?= $f['id'] ?>">

					<input type="text" name="nama" placeholder="Nama Lengkap" required value="<?= $f['nama'] ?>">
					
					<input type="text" name="nama_username" placeholder="Username" required value="<?= $f['username'] ?>">
					
					<input type="text" name="alamat" placeholder="Alamat" required value="<?= $f['alamat'] ?>">
					
					<input type="text" name="no_tlp" placeholder="No Telepon" required value="<?= $f['no_tlp'] ?>">
					
					<select name="status" required>
						<option value="">-- Pilih Status --</option>
						<option value="1" <?= $f['status'] == '1' ? 'selected' : '' ?>>Admin</option>
						<option value="2" <?= $f['status'] == '2' ? 'selected' : '' ?>>pemilik toko</option>
					</select>

					<input autocomplete="off" type="text" name="password" placeholder="Password">
					<label>* Password tidak bisa ditampilkan karena terenkripsi</label><br>
					<label>* Kosongkan form password jika tidak ingin merubah password</label><br><br>

					<button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
					<a href="users.php" class="btnblue" style="background: #f33155"><i class="fa fa-close"></i> Batal</a>
				</form>
			</div>
		</div>
	</div>
</div>
