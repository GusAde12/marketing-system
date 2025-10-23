<script type="text/javascript">
	document.title="Tambah Kasir";
	document.getElementById('users').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Tambah User</h3>
				<form class="form-input" method="post" action="handler.php?action=tambah_user">
					<input type="text" name="nama" placeholder="Nama" required="required">
					<input type="text" name="nama_username" placeholder="Username" required="required">
					<input type="text" name="alamat" placeholder="Alamat " required="required">
					<input type="text" name="no_tlp" placeholder="Telp" required="required">
					<input autocomplete="off" type="text" name="password" placeholder="Password" required="required">
					<!-- Tambahan opsi status -->
					<select name="status" required>
						<option value="">-- Pilih Status --</option>
						<option value="1">Admin</option>
						<option value="2">Pemilik Toko</option>
					</select>
					<button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
					<a href="users.php" class="btnblue" style="background: #f33155"><i class="fa fa-close"></i> Batal</a>
				</form>
			</div>
		</div>
	</div>
</div>
