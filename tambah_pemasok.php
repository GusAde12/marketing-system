<script type="text/javascript">
	document.title="Tambah Pemasok";
	document.getElementById('pemasok').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
            <h2>Form Input Sederhana</h2>
            <form class="form-input" method="post" action="handler.php?action=tambah_pemasok">
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" placeholder="nama" required >
        <label for="alamat">alamat</label>
        <input type="text" id="alamat" name="alamat" placeholder="alamat" required class="form-control">
        <label for="telp">Telp</label>
        <input type="number" id="telp" name="telp" placeholder="telp" required class="form-control">
        <label for="penanggung_jawab">Penanggung Jawab</label>
        <input type="text" id="penanggung_jawab" name="penanggung_jawab" placeholder="penanggung_jawab" required class="form-control">
        <label for="status">Status</label>
        <input type="text" id="status" name="status" placeholder="status" required class="form-control">
        </div>
    <button class="btnblue" type="submit">
            <i class="fa fa-save"></i> Simpan
        </button>
        </form>
        <a href="stok.php" class="btnblue" style="background: #f33155">
            <i class="fa fa-close"></i> Batal
        </a>

    
			</div>
		</div>
	</div>
</div>
