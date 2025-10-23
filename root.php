<?php 
// coded by https://www.athoul.site
error_reporting(0);
class penjualan
{
	
	public $con;
	function __construct()
	{
		$this->con=new mysqli("localhost","root","","imk");
	}
	function __destruct()
	{
		$this->con->close();
	}
	function alert($text){
		?><script type="text/javascript">
            alert( "<?= $text ?>" );
        </script>
        <?php
	}
	// coded by https://www.athoul.site
	function redirect($url){
		?>
		<script type="text/javascript">
		window.location.href="<?= $url ?>";
		</script>
		<?php
	}
	function go_back(){
		?>
		<script type="text/javascript">
		window.history.back();
		</script>
		<?php
	}
	function login($username, $password) {
		$error = [];
	
		if (trim($username) == "") {
			$error[] = "Username";
		}
	
		if (trim($password) == "") {
			$error[] = "Password";
		}
	
		if (!empty($error)) {
			echo "<div class='red'><i class='fa fa-warning'></i> Maaf, " . implode(' dan ', $error) . " tidak boleh kosong.</div>";
		} else {
			$password = sha1($password);
			$stmt = $this->con->prepare("SELECT * FROM user WHERE username=? AND password=?");
			$stmt->bind_param("ss", $username, $password);
			$stmt->execute();
			$result = $stmt->get_result();
	
			if ($result->num_rows > 0) {
				$data = $result->fetch_assoc();
	
				session_start();
				$_SESSION['username'] = $data['username'];
				$_SESSION['status'] = $data['status'];
				$_SESSION['id'] = $data['id'];
	
				echo "<div class='green'><i class='fa fa-check'></i> Login berhasil, silakan tunggu beberapa saat...</div>";
	
				// Arahkan berdasarkan role (status)
				if ($data['status'] == '1') {
					$this->redirect("home.php"); // admin
				} else {
					$this->redirect("hometoko.php"); // manajer
				}
	
			} else {
				echo "<div class='red'><i class='fa fa-warning'></i> Maaf, username atau password salah.</div>";
			}
	
			$stmt->close();
		}
	}
	
	function tambah_barang($nama_barang,$stok,$harga_beli,$harga_jual,$id_kategori,$id_pemasok){
		$query=$this->con->query("select * from barang where nama_barang='$nama_barang'");
		if ($query->num_rows > 0) {
			$this->alert("Data barang sudah ada");
			$this->go_back();
		}
		else{
			$query2=$this->con->query("insert into barang set nama_barang='$nama_barang',stok='$stok',harga_beli='$harga_beli',harga_jual='$harga_jual',id_kategori='$id_kategori',id_distributor='$id_pemasok'");
			if ($query2===TRUE) {
				$this->alert("Data Berhasil Ditambahkan");
				$this->redirect("barang.php");
			}
			else{
				$this->alert("Data Gagal Ditambahkan");
				$this->redirect("barang.php");
			}
		}
	}
	// public function tambah_pembelian($no_faktur, $distributor, $nama, $barang_lain, $id_barang, $jumlah, $harga, $total, $tanggal) {
	// 	// Cek apakah no_faktur_pembelian sudah ada
	// 	$query = $this->con->query("SELECT * FROM pembelian WHERE no_faktur_pembelian='$no_faktur'");
	// 	if ($query->num_rows > 0) {
	// 		// Jika no faktur sudah ada
	// 		$this->alert("No faktur pembelian sudah ada");
	// 		$this->go_back();
	// 	} else {
	// 		// Jika no faktur belum ada, lakukan insert
	// 		$query2 = $this->con->query("INSERT INTO pembelian (no_faktur_pembelian, distributor, nama, barang_lain, id_barang, jumlah, harga, total, tanggal) 
	// 									 VALUES ('$no_faktur', '$distributor', '$nama', '$barang_lain', '$id_barang', '$jumlah', '$harga', '$total', '$tanggal')");
			
	// 		// Cek apakah query berhasil
	// 		if ($query2 === TRUE) {
	// 			$this->alert("Pembelian berhasil ditambahkan");
	// 			$this->redirect("pembelian.php");
	// 		} else {
	// 			$this->alert("Pembelian gagal ditambahkan");
	// 			$this->redirect("pembelian.php");
	// 		}
	// 	}
	// }

	public function cek_faktur_ada($no_faktur) {
    $query = $this->con->query("SELECT no_faktur_pembelian FROM pembelian WHERE no_faktur_pembelian = '$no_faktur'");
    return $query->num_rows > 0;
}


	public function tambah_pembelian($no_faktur, $distributor, $nama, $barang_lain, $id_barang, $jumlah, $harga, $total, $tanggal) {
    // Cek apakah no faktur sudah ada di tabel pembelian
    $cek = $this->con->query("SELECT * FROM pembelian WHERE no_faktur_pembelian='$no_faktur'");
    
    if ($cek->num_rows == 0) {
        // Insert ke tabel pembelian (sekali saja)
        $this->con->query("INSERT INTO pembelian (no_faktur_pembelian, distributor, nama, barang_lain, tanggal) 
                           VALUES ('$no_faktur', '$distributor', '$nama', '$barang_lain', '$tanggal')");
    }

    // Masukkan detail barang, tambahkan kolom barang_lain
    $insert_detail = $this->con->query("INSERT INTO detail_pembelian 
        (no_faktur_pembelian, id_barang, barang_lain, jumlah, harga, total, tanggal) 
        VALUES ('$no_faktur', '$id_barang', '$barang_lain', '$jumlah', '$harga', '$total', '$tanggal')");

    if ($insert_detail === TRUE) {
        return true;
    } else {
        error_log("Gagal insert detail_pembelian: " . $this->con->error);
        return false;
    }
}



	public function tambah_pembelian_lain($no_faktur, $distributor, $nama, $barang_lain, $total, $tanggal) {
	$cek = $this->con->query("SELECT * FROM pembelian WHERE no_faktur_pembelian='$no_faktur'");
	
	if ($cek->num_rows == 0) {
		// Langsung simpan total pembelian sebagai satu record umum (tanpa detail barang)
		$this->con->query("INSERT INTO pembelian (no_faktur_pembelian, distributor, nama, barang_lain, total, tanggal) 
						   VALUES ('$no_faktur', '$distributor', '$nama', '$barang_lain', '$total', '$tanggal')");
	}
}


	
	function tambah_stok($id_barang, $tanggal, $stok_barang, $jumlah_asli, $selisih, $keterangan) {
		// Pastikan id_barang ada di tabel Barang
		$cek_barang = $this->con->query("SELECT * FROM barang WHERE id_barang='$id_barang'");
		if ($cek_barang->num_rows == 0) {
			$this->alert("Error: ID Barang tidak ditemukan.");
			$this->go_back();
			return;
		}
	
		// Format tanggal ke YYYY-MM-DD jika belum sesuai
		$tanggal = date("Y-m-d", strtotime($tanggal));
	
		// Query insert ke stok_opname (tanpa id_opname karena AUTO_INCREMENT)
		$query = $this->con->query("
			INSERT INTO stok_opname (id_barang, tanggal, stok_barang, jumlah_asli, selisih, keterangan) 
			VALUES ('$id_barang', '$tanggal', '$stok_barang', '$jumlah_asli', '$selisih', '$keterangan')
		") or die("Error SQL: " . $this->con->error);
	
		if ($query === TRUE) {
			$this->alert("✅ Stok opname berhasil ditambahkan!");
			$this->redirect("stok.php");
		} else {
			$this->alert("❌ Gagal menambahkan stok opname.");
			$this->redirect("stok.php");
		}
	}
	
	
	function tambah_distributor($nama_pemasok,$alamat,$telp,$nama_penanggung_jawab){
		$query=$this->con->query("select * from data_distributor where nama_penanggung_jawab='$nama_penanggung_jawab'");
		if ($query->num_rows > 0) {
			$this->alert("Data Nama Penanggung Jawab Sudah Ada");
			$this->go_back();
		}
		else{
			$query2=$this->con->query("insert into data_distributor set nama_pemasok='$nama_pemasok',alamat='$alamat',telp='$telp',nama_penanggung_jawab='$nama_penanggung_jawab'");
			if ($query2===TRUE) {
				$this->alert("Data Berhasil Ditambahkan");
				$this->redirect("distributor.php");
			}
			else{
				$this->alert("Data Gagal Ditambahkan");
				$this->redirect("distributor.php");
			}
		}
	}
	// function tambah_kasir($nama,$username,$alamat,$telp,$password,$status){
	// 	$query=$this->con->query("select * from user where nama,username,alamat,no_tlp,password,status='$nama','$username','$alamat','$telp','$password','$status'");
	// 	if ($query->num_rows > 0) {
	// 		$this->alert("User Sudah Ada");
	// 		$this->redirect("users.php");
	// 	}else{
	// 		$query2=$this->con->query("insert into user set nama='$nama',username='$username',alamat='$alamat',no_tlp='$telp',password='$password',status='$status'");
	// 		if ($query2===TRUE) {
	// 			$this->alert("Users Berhasil Ditambahkan");
	// 			$this->redirect("users.php");
	// 		}
	// 		else{
	// 			$this->alert("Users Gagal Ditambahkan");
	// 			$this->redirect("users.php");
	// 		}
	// 	}
	// }
	function tambah_user($nama, $username, $alamat, $telp, $password, $status) {
		$hashed_password = sha1($password); // gunakan SHA1
	
		// Cek apakah username sudah ada
		$query = $this->con->query("SELECT * FROM user WHERE username='$username'");
	
		if ($query && $query->num_rows > 0) {
			$this->alert("Username sudah digunakan");
			$this->redirect("users.php");
		} else {
			$query2 = $this->con->query("INSERT INTO user SET 
				nama='$nama', 
				username='$username', 
				alamat='$alamat', 
				no_tlp='$telp', 
				password='$hashed_password', 
				status='$status'");
	
			if ($query2 === TRUE) {
				$this->alert("Users Berhasil Ditambahkan");
			} else {
				$this->alert("Users Gagal Ditambahkan");
			}
			$this->redirect("users.php");
		}
	}
	

	// coded by https://www.athoul.site
	function tambah_kategori($nama_kategori){
		$query=$this->con->query("select * from kategori where nama_kategori='$nama_kategori'");
		if ($query->num_rows > 0) {
			$this->alert("Kategori Sudah Ada");
			$this->redirect("kategori.php");
		}else{
			$query2=$this->con->query("insert into kategori set nama_kategori='$nama_kategori'");
			if ($query2===TRUE) {
				$this->alert("kategori Berhasil Ditambahkan");
				$this->redirect("kategori.php");
			}
			else{
				$this->alert("kategori Gagal Ditambahkan");
				$this->redirect("kategori.php");
			}
		}
	}
	function tampil_barang($keyword){
		if ($keyword=="null") {
			$query=$this->con->query("select barang.id_barang,barang.nama_barang,barang.stok,barang.harga_beli,barang.harga_jual,barang.date_added,kategori.nama_kategori from barang inner join kategori on kategori.id_kategori=barang.id_kategori");
		}else{
			$query=$this->con->query("select barang.id_barang,barang.nama_barang,barang.stok,barang.harga_beli,barang.harga_jual,barang.date_added,kategori.nama_kategori from barang inner join kategori on kategori.id_kategori=barang.id_kategori where nama_barang like '%$keyword%'");
		}
		if ($query->num_rows > 0) {
			$no=1;
			while ($data=$query->fetch_assoc()) {
				?>
					<tr style="border-bottom: 1px solid #eee;">
						<td style="padding: 12px 15px;"><?= $no ?></td>
						<td style="padding: 12px 15px;"><?= $data['nama_barang'] ?></td>
						<td style="padding: 12px 15px;"><?= $data['nama_kategori'] ?></td>
						<td style="padding: 12px 15px;"><?= $data['stok'] ?></td>
						<td style="padding: 12px 15px;">Rp. <?= number_format($data['harga_beli']) ?></td>
						<td style="padding: 12px 15px;">Rp. <?= number_format($data['harga_jual']) ?></td>
						<td style="padding: 12px 15px;"><?= date("d-m-Y",strtotime($data['date_added'])) ?></td>
						<td style="padding: 12px 15px; text-align: center;">
							<div class="action-buttons" style="display: flex; justify-content: center; gap: 8px;">
								<a href="?action=edit_barang&id_barang=<?= $data['id_barang'] ?>" class="btn-action btn-edit" title="Edit">
									<i class="fa fa-pencil"></i>
								</a>
								<a href="handler.php?action=hapus_barang&id_barang=<?= $data['id_barang'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('yakin ingin menghapus <?= $data['nama_barang']." (id : ".$data['id_barang'] ?>) ?')">
									<i class="fa fa-trash"></i>
								</a>
							</div>
						</td>
					</tr>
					<?php
					$no++;
			}
		}else{
			echo '<tr><td colspan="8" style="padding: 20px; text-align: center; color: #999;"><i class="fa fa-info-circle" style="margin-right: 8px;"></i> Maaf, barang yang anda cari tidak ada!</td></tr>';
		}
	}
	
	function tampil_barang_filter($id_cat){
			$query=$this->con->query("select barang.id_barang,barang.nama_barang,barang.stok,barang.harga_beli,barang.harga_jual,barang.date_added,kategori.nama_kategori from barang inner join kategori on kategori.id_kategori=barang.id_kategori where kategori.id_kategori='$id_cat'");
			if ($query->num_rows > 0) {
				$no=1;
				while ($data=$query->fetch_assoc()) {
					?>
						<tr style="border-bottom: 1px solid #eee;">
							<td style="padding: 12px 15px;"><?= $no ?></td>
							<td style="padding: 12px 15px;"><?= $data['nama_barang'] ?></td>
							<td style="padding: 12px 15px;"><?= $data['nama_kategori'] ?></td>
							<td style="padding: 12px 15px;"><?= $data['stok'] ?></td>
							<td style="padding: 12px 15px;">Rp. <?= number_format($data['harga_beli']) ?></td>
							<td style="padding: 12px 15px;">Rp. <?= number_format($data['harga_jual']) ?></td>
							<td style="padding: 12px 15px;"><?= date("d-m-Y",strtotime($data['date_added'])) ?></td>
							<td style="padding: 12px 15px; text-align: center;">
								<div class="action-buttons" style="display: flex; justify-content: center; gap: 8px;">
									<a href="?action=edit_barang&id_barang=<?= $data['id_barang'] ?>" class="btn-action btn-edit" title="Edit">
										<i class="fa fa-pencil"></i>
									</a>
									<a href="handler.php?action=hapus_barang&id_barang=<?= $data['id_barang'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('yakin ingin menghapus <?= $data['nama_barang']." (id : ".$data['id_barang'] ?>) ?')">
										<i class="fa fa-trash"></i>
									</a>
								</div>
							</td>
						</tr>
						<?php
						$no++;
				}
			}else{
				echo '<tr><td colspan="8" style="padding: 20px; text-align: center; color: #999;"><i class="fa fa-info-circle" style="margin-right: 8px;"></i> Maaf, barang yang anda cari tidak ada!</td></tr>';
			}
		}

		function tampil_barang_dropdown($keyword){
		if ($keyword=="null") {
			$query = $this->con->query("
				SELECT b.id_barang, b.nama_barang, b.stok, b.harga_beli, b.harga_jual, b.date_added, k.nama_kategori
				FROM barang b
				INNER JOIN kategori k ON k.id_kategori = b.id_kategori
				WHERE b.id_barang IN (SELECT DISTINCT id_barang_jadi FROM detail_produksi)
			");
		} else {
			$query = $this->con->query("
				SELECT b.id_barang, b.nama_barang, b.stok, b.harga_beli, b.harga_jual, b.date_added, k.nama_kategori
				FROM barang b
				INNER JOIN kategori k ON k.id_kategori = b.id_kategori
				WHERE b.id_barang IN (SELECT DISTINCT id_barang_jadi FROM detail_produksi)
				AND b.nama_barang LIKE '%$keyword%'
			");
		}
		
		if ($query->num_rows > 0) {
			$no = 1;
			while ($data = $query->fetch_assoc()) {
				?>
				<tr style="border-bottom: 1px solid #eee;">
					<td style="padding: 12px 15px;"><?= $no ?></td>
					<td style="padding: 12px 15px;"><?= $data['nama_barang'] ?></td>
					<td style="padding: 12px 15px;"><?= $data['nama_kategori'] ?></td>
					<td style="padding: 12px 15px;"><?= $data['stok'] ?></td>
					<td style="padding: 12px 15px;">Rp. <?= number_format($data['harga_beli']) ?></td>
					<td style="padding: 12px 15px;">Rp. <?= number_format($data['harga_jual']) ?></td>
					<td style="padding: 12px 15px;"><?= date("d-m-Y",strtotime($data['date_added'])) ?></td>
					<td style="padding: 12px 15px; text-align: center;">
						<div class="action-buttons" style="display: flex; justify-content: center; gap: 8px;">
							<a href="?action=edit_barang&id_barang=<?= $data['id_barang'] ?>" class="btn-action btn-edit" title="Edit">
								<i class="fa fa-pencil"></i>
							</a>
							<a href="handler.php?action=hapus_barang&id_barang=<?= $data['id_barang'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('yakin ingin menghapus <?= $data['nama_barang']." (id : ".$data['id_barang'] ?>) ?')">
								<i class="fa fa-trash"></i>
							</a>
						</div>
					</td>
				</tr>
				<?php
				$no++;
			}
		} else {
			echo '<tr><td colspan="8" style="padding: 20px; text-align: center; color: #999;"><i class="fa fa-info-circle" style="margin-right: 8px;"></i> Maaf, barang yang anda cari tidak ada!</td></tr>';
		}
	}

	function tampil_barang_dropdownd_filter($id_cat){
	$query = $this->con->query("
		SELECT b.id_barang, b.nama_barang, b.stok, b.harga_beli, b.harga_jual, b.date_added, k.nama_kategori
		FROM barang b
		INNER JOIN kategori k ON k.id_kategori = b.id_kategori
		WHERE b.id_kategori = '$id_cat'
		AND b.id_barang IN (SELECT DISTINCT id_barang_jadi FROM detail_produksi)
	");

	if ($query->num_rows > 0) {
		$no=1;
		while ($data=$query->fetch_assoc()) {
			?>
			<tr style="border-bottom: 1px solid #eee;">
				<td style="padding: 12px 15px;"><?= $no ?></td>
				<td style="padding: 12px 15px;"><?= $data['nama_barang'] ?></td>
				<td style="padding: 12px 15px;"><?= $data['nama_kategori'] ?></td>
				<td style="padding: 12px 15px;"><?= $data['stok'] ?></td>
				<td style="padding: 12px 15px;">Rp. <?= number_format($data['harga_beli']) ?></td>
				<td style="padding: 12px 15px;">Rp. <?= number_format($data['harga_jual']) ?></td>
				<td style="padding: 12px 15px;"><?= date("d-m-Y",strtotime($data['date_added'])) ?></td>
				<td style="padding: 12px 15px; text-align: center;">
					<div class="action-buttons" style="display: flex; justify-content: center; gap: 8px;">
						<a href="?action=edit_barang&id_barang=<?= $data['id_barang'] ?>" class="btn-action btn-edit" title="Edit">
							<i class="fa fa-pencil"></i>
						</a>
						<a href="handler.php?action=hapus_barang&id_barang=<?= $data['id_barang'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('yakin ingin menghapus <?= $data['nama_barang']." (id : ".$data['id_barang'] ?>) ?')">
							<i class="fa fa-trash"></i>
						</a>
					</div>
				</td>
			</tr>
			<?php
			$no++;
		}
	} else {
		echo '<tr><td colspan="8" style="padding: 20px; text-align: center; color: #999;"><i class="fa fa-info-circle" style="margin-right: 8px;"></i> Maaf, barang yang anda cari tidak ada!</td></tr>';
	}
}

	public function update_total_pembelian($no_faktur, $total) {
    $stmt = $this->con->prepare("UPDATE pembelian SET total = ?, tanggal = CURDATE() WHERE no_faktur_pembelian = ?");
    $stmt->bind_param("ds", $total, $no_faktur);
    $stmt->execute();
    $stmt->close();
}




	



	function tampil_pembelian($keyword) {
    $keyword = $this->con->real_escape_string($keyword);

    $sql = "
        SELECT 
			p.no_faktur_pembelian, 
			p.distributor,
			p.nama,
			p.barang_lain,
			p.tanggal,
			(
				SELECT COUNT(*) 
				FROM detail_pembelian d 
				WHERE d.no_faktur_pembelian = p.no_faktur_pembelian
			) AS jumlah,
			(
				SELECT SUM(d.total) 
				FROM detail_pembelian d 
				WHERE d.no_faktur_pembelian = p.no_faktur_pembelian
			) AS total
		FROM pembelian p

    ";

    if ($keyword !== "null") {
        $sql .= " WHERE 
            p.no_faktur_pembelian LIKE '%$keyword%' 
            OR p.distributor LIKE '%$keyword%' 
            OR p.nama LIKE '%$keyword%' 
            OR p.barang_lain LIKE '%$keyword%'";
    }

    $sql .= " GROUP BY p.no_faktur_pembelian ORDER BY p.id_pembelian DESC";

    $query = $this->con->query($sql);

    if ($query && $query->num_rows > 0) {
        $no = 1;
        while ($data = $query->fetch_assoc()) {
            $is_penjual_lain = ($data['distributor'] == 'penjual lain');
            ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px 15px; text-align: center;"><?= $no++ ?></td>
                <td style="padding: 12px 15px;"><span class="badge-faktur"><?= htmlspecialchars($data['no_faktur_pembelian']) ?></span></td>
                <td style="padding: 12px 15px;">
                    <?= $is_penjual_lain 
                        ? '<span class="badge-penjual-lain">'.htmlspecialchars($data['distributor']).'</span>' 
                        : '<span class="badge-distributor">'.htmlspecialchars($data['distributor']).'</span>'; ?>
                </td>
                <td style="padding: 12px 15px;">
                    <?= $is_penjual_lain && $data['nama'] ? '<i class="fa fa-user"></i> '.htmlspecialchars($data['nama']) : '<span style="color:#6c757d;">-</span>'; ?>
                </td>
                <!-- <td style="padding: 12px 15px;">
                    <?= $is_penjual_lain && $data['barang_lain'] ? '<i class="fa fa-tag"></i> '.htmlspecialchars($data['barang_lain']) : '<span style="color:#6c757d;">-</span>'; ?>
                </td> -->
                <td style="padding: 12px 15px; text-align: center;">
                    <span class="badge-jumlah"><?= (int)$data['jumlah'] ?></span>
                </td>
                <td style="padding: 12px 15px;"><span class="text-date"><?= date("d M Y", strtotime($data['tanggal'])) ?></span></td>
                <td style="padding: 12px 15px; text-align: right;">
                    <span class="text-total">Rp <?= number_format($data['total'], 0, ',', '.') ?></span>
                </td>
                <td style="padding: 12px 15px; text-align: center;">
                    <div class="action-buttons">
                        <a href="?action=detail_pembelian&faktur=<?= urlencode($data['no_faktur_pembelian']) ?>" class="btn-action btn-detail" title="Detail"><i class="fa fa-eye"></i></a>
                        
                        <a href="handler.php?action=hapus_produksi&faktur=<?= urlencode($data['no_faktur_pembelian']) ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Hapus pembelian faktur <?= htmlspecialchars($data['no_faktur_pembelian']) ?>?')"><i class="fa fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php
        }
    } else {
        echo '<tr><td colspan="10" style="padding: 40px 15px; text-align: center; color: #6c757d;">
            <div class="empty-state">
                <i class="fa fa-search fa-3x" style="margin-bottom: 15px;"></i>
                <h5 style="margin-bottom: 10px; font-weight: 500;">Data tidak ditemukan</h5>
                <p style="margin: 0;">Tidak ada data pembelian yang sesuai dengan pencarian Anda</p>
            </div>
        </td></tr>';
    }
}



		
		
	
	function tampil_pembelian_filter($distributor){
		$query = $this->con->query("SELECT id_pembelian, no_faktur_pembelian, distributor, nama, tanggal, total FROM pembelian WHERE distributor='$distributor'");
		
		if ($query->num_rows > 0) {
			$no = 1;
			while ($data = $query->fetch_assoc()) {
				?>
				<tr>
					<td><?= $no ?></td>
					<td><?= $data['no_faktur_pembelian'] ?></td>
					<td><?= $data['distributor'] ?></td>
					<td><?= $data['nama'] ?></td>
					<td><?= date("d-m-Y", strtotime($data['tanggal'])) ?></td>
					<td>Rp. <?= number_format($data['total']) ?></td>
					<td>
						<a href="?action=edit_pembelian&id_pembelian=<?= $data['id_pembelian'] ?>" class="btn bluetbl m-r-10"><span class="btn-edit-tooltip">Edit</span><i class="fa fa-pencil"></i></a>
						<a href="handler.php?action=hapus_pembelian&id_pembelian=<?= $data['id_pembelian'] ?>" class="btn redtbl" onclick="return confirm('Yakin ingin menghapus pembelian dengan faktur <?= $data['no_faktur_pembelian'] ?>?')"><span class="btn-hapus-tooltip">Hapus</span><i class="fa fa-trash"></i></a>
						<a href="?action=detail_pembelian&id_pembelian=<?= $data['id_pembelian'] ?>" class="btn greentbl"><span class="btn-detail-tooltip">Detail</span><i class="fa fa-eye"></i></a>
					
					</td>
				</tr>
				<?php
				$no++;
			}
		} else {
			echo "<td></td><td colspan='6'>Tidak ada pembelian dari distributor tersebut.</td>";
		}
	}
	

	function tampil_stok($keyword){
		if ($keyword=="null") {
			$query=$this->con->query("SELECT stok_opname.id_opname, stok_opname.id_barang, barang.nama_barang, stok_opname.tanggal, stok_opname.stok_barang, stok_opname.jumlah_asli, stok_opname.selisih, stok_opname.keterangan 
									  FROM stok_opname 
									  INNER JOIN barang ON stok_opname.id_barang = barang.id_barang");
		} else {
			$query=$this->con->query("SELECT stok_opname.id_opname, stok_opname.id_barang, barang.nama_barang, stok_opname.tanggal, stok_opname.stok_barang, stok_opname.jumlah_asli, stok_opname.selisih, stok_opname.keterangan 
									  FROM stok_opname 
									  INNER JOIN barang ON stok_opname.id_barang = barang.id_barang 
									  WHERE barang.nama_barang LIKE '%$keyword%'");
		}
		
		if ($query->num_rows > 0) {
			$no=1;
			while ($data=$query->fetch_assoc()) {
				// Determine selisih class
				$selisihClass = '';
				if ($data['selisih'] > 0) {
					$selisihClass = 'selisih-positive';
				} elseif ($data['selisih'] < 0) {
					$selisihClass = 'selisih-negative';
				} else {
					$selisihClass = 'selisih-neutral';
				}
				?>
				<tr style="border-bottom: 1px solid #eee;">
					<td style="padding: 12px 15px; text-align: center;"><?= $no ?></td>
					<td style="padding: 12px 15px;"><?= htmlspecialchars($data['nama_barang']) ?></td>
					<td style="padding: 12px 15px;"><?= date("d-m-Y", strtotime($data['tanggal'])) ?></td>
					<td style="padding: 12px 15px; text-align: center;"><?= htmlspecialchars($data['stok_barang']) ?></td>
					<td style="padding: 12px 15px; text-align: center;"><?= htmlspecialchars($data['jumlah_asli']) ?></td>
					<td style="padding: 12px 15px; text-align: center;" class="<?= $selisihClass ?>">
						<?= htmlspecialchars($data['selisih']) ?>
					</td>
					<td style="padding: 12px 15px;"><?= htmlspecialchars($data['keterangan']) ?></td>
					<td style="padding: 12px 15px; text-align: center;">
						<div class="action-buttons">
							<!-- <a href="?action=edit_stok&id_opname=<?= $data['id_opname'] ?>" 
							   class="btn-action btn-edit" 
							   title="Edit">
								<i class="fa fa-pencil"></i>
							</a> -->
							<a href="handler.php?action=hapus_stok&id_opname=<?= $data['id_opname'] ?>" 
							   class="btn-action btn-delete" 
							   title="Hapus"
							   onclick="return confirm('Yakin ingin menghapus data stok <?= htmlspecialchars($data['nama_barang']) ?>?')">
								<i class="fa fa-trash"></i>
							</a>
						</div>
					</td>
				</tr>
				<?php
				$no++;
			}
		} else {
			echo '<tr><td colspan="8" style="padding: 20px; text-align: center; color: #6c757d;">
					<div class="empty-state">
						<i class="fa fa-search fa-3x" style="margin-bottom: 15px;"></i>
						<h5 style="margin-bottom: 10px; font-weight: 500;">Data tidak ditemukan</h5>
						<p style="margin: 0;">Tidak ada data stok yang sesuai dengan pencarian Anda</p>
					</div>
				  </td></tr>';
		}
	}
	
	function tampil_stok_filter($id_barang){
		$query=$this->con->query("SELECT stok_opname.id_opname, stok_opname.id_barang, barang.nama_barang, stok_opname.tanggal, stok_opname.stok_barang, stok_opname.jumlah_asli, stok_opname.selisih, stok_opname.keterangan 
								  FROM stok_opname 
								  INNER JOIN barang ON stok_opname.id_barang = barang.id_barang 
								  WHERE stok_opname.id_barang='$id_barang'");
		if ($query->num_rows > 0) {
			$no=1;
			while ($data=$query->fetch_assoc()) {
				// Determine selisih class
				$selisihClass = '';
				if ($data['selisih'] > 0) {
					$selisihClass = 'selisih-positive';
				} elseif ($data['selisih'] < 0) {
					$selisihClass = 'selisih-negative';
				} else {
					$selisihClass = 'selisih-neutral';
				}
				?>
				<tr style="border-bottom: 1px solid #eee;">
					<td style="padding: 12px 15px; text-align: center;"><?= $no ?></td>
					<td style="padding: 12px 15px;"><?= htmlspecialchars($data['nama_barang']) ?></td>
					<td style="padding: 12px 15px;"><?= date("d-m-Y", strtotime($data['tanggal'])) ?></td>
					<td style="padding: 12px 15px; text-align: center;"><?= htmlspecialchars($data['stok_barang']) ?></td>
					<td style="padding: 12px 15px; text-align: center;"><?= htmlspecialchars($data['jumlah_asli']) ?></td>
					<td style="padding: 12px 15px; text-align: center;" class="<?= $selisihClass ?>">
						<?= htmlspecialchars($data['selisih']) ?>
					</td>
					<td style="padding: 12px 15px;"><?= htmlspecialchars($data['keterangan']) ?></td>
					<td style="padding: 12px 15px; text-align: center;">
						<div class="action-buttons">
							<a href="?action=edit_stok&id_opname=<?= $data['id_opname'] ?>" 
							   class="btn-action btn-edit" 
							   title="Edit">
								<i class="fa fa-pencil"></i>
							</a>
							<a href="handler.php?action=hapus_stok&id_opname=<?= $data['id_opname'] ?>" 
							   class="btn-action btn-delete" 
							   title="Hapus"
							   onclick="return confirm('Yakin ingin menghapus data stok <?= htmlspecialchars($data['nama_barang']) ?>?')">
								<i class="fa fa-trash"></i>
							</a>
						</div>
					</td>
				</tr>
				<?php
				$no++;
			}
		} else {
			echo '<tr><td colspan="8" style="padding: 20px; text-align: center; color: #6c757d;">
					<div class="empty-state">
						<i class="fa fa-search fa-3x" style="margin-bottom: 15px;"></i>
						<h5 style="margin-bottom: 10px; font-weight: 500;">Data tidak ditemukan</h5>
						<p style="margin: 0;">Tidak ada data stok yang sesuai dengan pencarian Anda</p>
					</div>
				  </td></tr>';
		}
	}
	
	

	function tampil_distributor($keyword){
		if ($keyword=="null") {
			$query=$this->con->query("select data_distributor.id_distributor,data_distributor.nama_pemasok,data_distributor.alamat,data_distributor.telp,data_distributor.nama_penanggung_jawab,data_distributor.status from data_distributor");
		}else{
			$query=$this->con->query("select data_distributor.id_distributor,data_distributor.nama_pemasok,data_distributor.alamat,data_distributor.telp,data_distributor.nama_penanggung_jawab,data_distributor.status from data_distributor where Nama_Pemasok like '%$keyword%'");
		}
		if ($query->num_rows > 0) {
			$no=1;
			while ($data=$query->fetch_assoc()) {
				$statusClass = $data['status'] == 'Aktif' ? 'status-active' : 'status-inactive';
				?>
					<tr style="border-bottom: 1px solid #eee;">
						<td style="padding: 12px 15px;"><?= $no ?></td>
						<td style="padding: 12px 15px;"><?= $data['nama_pemasok'] ?></td>
						<td style="padding: 12px 15px;"><?= $data['alamat'] ?></td>
						<td style="padding: 12px 15px;"><?= $data['telp'] ?></td>
						<td style="padding: 12px 15px;"><?= $data['nama_penanggung_jawab'] ?></td>
						<!-- <td style="padding: 12px 15px;">
							<span class="status-badge <?= $statusClass ?>">
								<?= $data['status'] ?>
							</span>
						</td> -->
						<td style="padding: 12px 15px; text-align: center;">
							<div class="action-buttons" style="display: flex; justify-content: center; gap: 8px;">
								<a href="?action=edit_distributor&id_distributor=<?= $data['id_distributor'] ?>" class="btn-action btn-edit" title="Edit">
									<i class="fa fa-pencil"></i>
								</a>
								<a href="handler.php?action=hapus_distributor&id_distributor=<?= $data['id_distributor'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus distributor <?= $data['nama_pemasok'] ?>?')">
									<i class="fa fa-trash"></i>
								</a>
							</div>
						</td>
					</tr>
					<?php
					$no++;
			}
		}else{
			echo '<tr><td colspan="7" style="padding: 20px; text-align: center; color: #999;"><i class="fa fa-info-circle" style="margin-right: 8px;"></i> Maaf, data yang anda cari tidak ada!</td></tr>';
		}
	}
	
	function tampil_distributor_filter($id_distributor){
			$query=$this->con->query("select data_distributor.id_distributor,data_distributor.Nama_pemasok,data_distributor.Alamat,data_distributor.Telp,data_distributor.Nama_Penanggung_Jawab,data_distributor.Status from data_distributor where id_distributor='$id_distributor'");
			if ($query->num_rows > 0) {
				$no=1;
				while ($data=$query->fetch_assoc()) {
					$statusClass = $data['status'] == 'Aktif' ? 'status-active' : 'status-inactive';
					?>
						<tr style="border-bottom: 1px solid #eee;">
							<td style="padding: 12px 15px;"><?= $no ?></td>
							<td style="padding: 12px 15px;"><?= $data['nama_pemasok'] ?></td>
							<td style="padding: 12px 15px;"><?= $data['alamat'] ?></td>
							<td style="padding: 12px 15px;"><?= $data['telp'] ?></td>
							<td style="padding: 12px 15px;"><?= $data['nama_penanggung_jawab'] ?></td>
							<!-- <td style="padding: 12px 15px;">
								<span class="status-badge <?= $statusClass ?>">
									<?= $data['status'] ?>
								</span>
							</td> -->
							<td style="padding: 12px 15px; text-align: center;">
								<div class="action-buttons" style="display: flex; justify-content: center; gap: 8px;">
									<a href="?action=edit_distributor&id_distributor=<?= $data['id_distributor'] ?>" class="btn-action btn-edit" title="Edit">
										<i class="fa fa-pencil"></i>
									</a>
									<a href="handler.php?action=hapus_distributor&id_distributor=<?= $data['id_distributor'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus distributor <?= $data['nama_pemasok'] ?>?')">
										<i class="fa fa-trash"></i>
									</a>
								</div>
							</td>
						</tr>
						<?php
						$no++;
				}
			}else{
				echo '<tr><td colspan="7" style="padding: 20px; text-align: center; color: #999;"><i class="fa fa-info-circle" style="margin-right: 8px;"></i> Maaf, data yang anda cari tidak ada!</td></tr>';
			}
		}
	
	function tampil_distributor2() {
		$query = $this->con->query("SELECT * FROM data_distributor ORDER BY id_distributor DESC");
		while ($data = $query->fetch_assoc()) {
			echo '<option value="' . $data['nama_pemasok'] . '" data-id="' . $data['id_distributor'] . '">' . $data['nama_pemasok'] . '</option>';
		}
	}

	function tampil_distributor3() {
		$query = $this->con->query("SELECT * FROM data_distributor ORDER BY id_distributor DESC");
		while ($data = $query->fetch_assoc()) {
			echo '<option value="' . $data['id_distributor'] . '">' . $data['nama_pemasok'] . '</option>';
		}
	}
	

	// function tampil_barang2() {
	// 	$query = $this->con->query("SELECT * FROM barang ORDER BY id_barang DESC");
	// 	while ($data = $query->fetch_assoc()) {
	// 		echo '<option value="' . $data['id_barang'] . '" data-id="' . $data['id_barang'] . '">' . $data['nama_barang'] . '</option>';
	// 	}
	// }

	function tampil_barang2() {
	$query = $this->con->query("SELECT * FROM barang WHERE tipe_barang = 'baku' ORDER BY id_barang DESC");
	while ($data = $query->fetch_assoc()) {
		echo '<option value="' . $data['id_barang'] . '" data-id="' . $data['id_barang'] . '">' . $data['nama_barang'] . '</option>';
	}
}
	function tampil_barang_jadi_dropdown() {
	$query = $this->con->query("SELECT * FROM barang WHERE tipe_barang = 'jadi' ORDER BY nama_barang ASC");
	while ($data = $query->fetch_assoc()) {
		echo '<option value="' . $data['id_barang'] . '">' . $data['nama_barang'] . '</option>';
	}
}


	
	// Fungsi untuk mendapatkan stok barang berdasarkan id_barang
	function get_stok_barang($id_barang) {
		$query = "SELECT stok FROM barang WHERE id_barang = ?";
		$stmt = $this->con->prepare($query);
		$stmt->bind_param("i", $id_barang);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		return isset($row['stok']) ? $row['stok'] : 0; // Memastikan stok ada, jika tidak ada kembalikan 0
	}


	// Fungsi untuk update stok barang
	function update_stok_barang($id_barang, $new_stok) {
		$query = "UPDATE barang SET stok = ? WHERE id_barang = ?";
		$stmt = $this->con->prepare($query);
		$stmt->bind_param("ii", $new_stok, $id_barang);
		$stmt->execute();
	}
	

	function tampil_kategori(){
		$query=$this->con->query("select * from kategori order by id_kategori desc");
		$no=1;
		while ($data=$query->fetch_assoc()) {
			?>
				<tr style="border-bottom: 1px solid #eee;">
					<td style="padding: 12px 15px;"><?= $no ?></td>
					<td style="padding: 12px 15px;"><?= $data['nama_kategori'] ?></td>
					<td style="padding: 12px 15px; text-align: center;">
						<div class="action-buttons" style="display: flex; justify-content: center; gap: 8px;">
							<a href="?action=edit_kategori&id_kategori=<?= $data['id_kategori'] ?>" class="btn-action btn-edit" title="Edit">
								<i class="fa fa-pencil"></i>
							</a>
							<a href="handler.php?action=hapus_kategori&id_kategori=<?= $data['id_kategori'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus kategori: <?= $data['nama_kategori'] ?>?')">
								<i class="fa fa-trash"></i>
							</a>
						</div>
					</td>
				</tr>
				<?php
			$no++;
		}
	}
	function tampil_kategori2(){
		$query=$this->con->query("select * from kategori order by id_kategori desc");
		while ($data=$query->fetch_assoc()) {
			?>
				<option value="<?= $data['id_kategori'] ?>"><?= $data['nama_kategori'] ?></option>
			<?php
		}
	}
	function tampil_kategori3($id_barang){
		$q=$this->con->query("select * from barang where id_barang='$id_barang'");
		$q2=$q->fetch_assoc();
		$id_cat=$q2['id_kategori'];
		$query=$this->con->query("select * from kategori order by id_kategori desc");
		while ($data=$query->fetch_assoc()) {
			?>
				<option <?php if ($data['id_kategori']==$id_cat) { echo "selected"; } ?> value="<?= $data['id_kategori'] ?>"><?= $data['nama_kategori'] ?></option>
			<?php
		}
	}
	function tampil_user(){
		$query = $this->con->query("SELECT * FROM user"); // Only showing Pemilik Toko (status 2)
		$no = 1;
		while ($data = $query->fetch_assoc()) {
			// Determine status class and text
			$statusClass = '';
			$statusText = '';
			
			switch ($data['status']) {
				case '1':
					$statusClass = 'status-admin';
					$statusText = 'Admin';
					break;
				case '2':
					$statusClass = 'status-pemilik';
					$statusText = 'Pemilik Toko';
					break;
				default:
					$statusClass = 'status-inactive';
					$statusText = 'Nonaktif';
					break;
			}
			?>
			<tr style="border-bottom: 1px solid #eee;">
				<td style="padding: 12px 15px; text-align: center;"><?= $no ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($data['nama']) ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($data['username']) ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($data['alamat']) ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($data['no_tlp']) ?></td>
				<td style="padding: 12px 15px; text-align: center;">
					<span class="status-badge <?= $statusClass ?>">
						<?= $statusText ?>
					</span>
				</td>
				<td style="padding: 12px 15px;">
					<span class="text-date"><?= date("d M Y", strtotime($data['date_created'])) ?></span>
				</td>
				<td style="padding: 12px 15px; text-align: center;">
					<div class="action-buttons">
						<a href="?action=edit_user&id=<?= $data['id'] ?>" 
						   class="btn-action btn-edit" 
						   title="Edit">
							<i class="fa fa-pencil"></i>
						</a>
						<a href="handler.php?action=hapus_user&id_user=<?= $data['id'] ?>" 
						   class="btn-action btn-delete" 
						   title="Hapus"
						   onclick="return confirm('Yakin ingin menghapus user <?= htmlspecialchars($data['username']) ?>?')">
							<i class="fa fa-trash"></i>
						</a>
					</div>
				</td>
			</tr>
			<?php
			$no++;
		}
	}
	function tampil_pemasok(){
		$query=$this->con->query("select * from pemasok ");
		$no=1;
		while ($data=$query->fetch_assoc()) {
			?>
			<tr>
					<td><?= $no ?></td>
					<td><?= $data['nama'] ?></td>
					<td><?= $data['alamat'] ?></td>
					<td><?= $data['telp'] ?></td>
					<td><?= $data['penanggung_jawab'] ?></td>
					<td><?= $data['status']?></td>
					<td><?= date("d-m-Y",strtotime($data['date_created'])) ?></td>
					<td>
						<a href="?action=edit_pemasok&id_kasir=<?= $data['id_pemasok'] ?>" class="btn bluetbl m-r-10"><span class="btn-edit-tooltip">Edit</span><i class="fa fa-pencil"></i></a>
						<a href="handler.php?action=hapus_user&id_user=<?= $data['id_pemasok'] ?>" class="btn redtbl" onclick="return confirm('yakin ingin menghapus user : <?= $data['username'] ?> ?')"><span class="btn-hapus-tooltip">Hapus</span><i class="fa fa-trash"></i></a>
					</td>
			</tr>
			<?php
			$no++;
		}
	}
	function tampil_laporan(){
		$query=$this->con->query("select transaksi.id_transaksi,transaksi.tgl_transaksi,transaksi.status_bayar,transaksi.no_invoice,transaksi.total_bayar,transaksi.nama_pembeli,user.username from transaksi inner join user on transaksi.kode_kasir=user.id order by transaksi.id_transaksi desc");
		$no=1;
		while ($f=$query->fetch_assoc()) {
			$statusClass = '';
			switch(strtolower($f['status_bayar'])) {
				case 'lunas':
					$statusClass = 'status-lunas';
					break;
				case 'dp':
					$statusClass = 'status-dp';
					break;
				case 'hutang':
					$statusClass = 'status-hutang';
					break;
				default:
					$statusClass = '';
			}
			?>
			<tr style="border-bottom: 1px solid #eee;">
				<td style="padding: 12px 15px; text-align: center;"><?= $no++ ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($f['no_invoice']) ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($f['username']) ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($f['nama_pembeli']) ?></td>
				<td style="padding: 12px 15px;"><?= date("d-m-Y",strtotime($f['tgl_transaksi'])) ?></td>
				<td style="padding: 12px 15px; text-align: right;">Rp <?= number_format($f['total_bayar'], 0, ',', '.') ?></td>
				<td style="padding: 12px 15px; text-align: center;">
					<span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($f['status_bayar']) ?></span>
				</td>
				<td style="padding: 12px 15px; text-align: center;">
					<div class="action-buttons">
						<a href="?action=detail_transaksi&id_transaksi=<?= $f['id_transaksi'] ?>" 
						   class="btn-action btn-view" 
						   title="Lihat">
							<i class="fa fa-eye"></i>
						</a>
						<a href="hapus_transaksi.php?id=<?= $f['id_transaksi'] ?>" 
						class="btn-action btn-delete" 
						title="Hapus"
						onclick="return confirm('Yakin ingin menghapus transaksi <?= htmlspecialchars($f['no_invoice']) ?>?')">
							<i class="fa fa-trash"></i>
						</a>
					</div>
				</td>
			</tr>
			<?php
		}
	}
	function filter_tampil_laporan($tanggal,$aksi){
		if ($aksi==1) {
			$split1=explode('-',$tanggal);
			$tanggal=$split1[2]."-".$split1[1]."-".$split1[0];
			$query=$this->con->query("select transaksi.id_transaksi,transaksi.tgl_transaksi,transaksi.status_bayar,transaksi.no_invoice,transaksi.total_bayar,transaksi.nama_pembeli,user.username from transaksi inner join user on transaksi.kode_kasir=user.id where transaksi.tgl_transaksi like '%$tanggal%' order by transaksi.id_transaksi desc");
		}else{
			$split1=explode('-',$tanggal);
			$tanggal=$split1[1]."-".$split1[0];
			$query=$this->con->query("select transaksi.id_transaksi,transaksi.tgl_transaksi,transaksi.status_bayar,transaksi.no_invoice,transaksi.total_bayar,transaksi.nama_pembeli,user.username from transaksi inner join user on transaksi.kode_kasir=user.id where transaksi.tgl_transaksi like '%$tanggal%' order by transaksi.id_transaksi desc");
		}
		
		$no=1;
		while ($f=$query->fetch_assoc()) {
			$statusClass = '';
			switch(strtolower($f['status_bayar'])) {
				case 'lunas':
					$statusClass = 'status-lunas';
					break;
				case 'dp':
					$statusClass = 'status-dp';
					break;
				case 'hutang':
					$statusClass = 'status-hutang';
					break;
				default:
					$statusClass = '';
			}
			?>
			<tr style="border-bottom: 1px solid #eee;">
				<td style="padding: 12px 15px; text-align: center;"><?= $no++ ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($f['no_invoice']) ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($f['username']) ?></td>
				<td style="padding: 12px 15px;"><?= htmlspecialchars($f['nama_pembeli']) ?></td>
				<td style="padding: 12px 15px;"><?= date("d-m-Y",strtotime($f['tgl_transaksi'])) ?></td>
				<td style="padding: 12px 15px; text-align: right;">Rp <?= number_format($f['total_bayar'], 0, ',', '.') ?></td>
				<td style="padding: 12px 15px; text-align: center;">
					<span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($f['status_bayar']) ?></span>
				</td>
				<td style="padding: 12px 15px; text-align: center;">
					<div class="action-buttons">
						<a href="?action=detail_transaksi&id_transaksi=<?= $f['id_transaksi'] ?>" 
						   class="btn-action btn-view" 
						   title="Lihat">
							<i class="fa fa-eye"></i>
						</a>
						<a onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars($f['no_invoice'])." (id : ".$f['id_transaksi'] ?>) ?')" 
						   href="handler.php?action=delete_transaksi&id=<?= $f['id_transaksi'] ?>" 
						   class="btn-action btn-delete" 
						   title="Hapus">
							<i class="fa fa-trash"></i>
						</a>
					</div>
				</td>
			</tr>
			<?php
		}
	}
	function show_jumlah_cat(){
		$query=$this->con->query("select * from kategori");
		echo $query->num_rows;
	}
	function show_jumlah_barang(){
		$query=$this->con->query("select * from barang");
		echo $query->num_rows;
	}
	function show_jumlah_distributor(){
		$query=$this->con->query("select * from data_distributor");
		echo $query->num_rows;
	}
	function show_jumlah_pembelian(){
		$query=$this->con->query("select * from pembelian");
		echo $query->num_rows;
	}
	function show_jumlah_kasir(){
		$query=$this->con->query("select * from user");
		echo $query->num_rows;
	}
	function show_jumlah_trans(){
		$query=$this->con->query("select * from transaksi where kode_kasir='$_SESSION[id]'");
		echo $query->num_rows;
	}
	function show_jumlah_trans2(){
		$query=$this->con->query("select * from transaksi");
		echo $query->num_rows;
	}
	function hapus_distributor($id_distributor){
		$query=$this->con->query("delete from data_distributor where id_distributor='$id_distributor'");
		if ($query === TRUE) {
			$this->alert("distributor id $id_distributor telah dihapus");
			$this->redirect("distributor.php");
		}
	}
	function hapus_laporan($id_transaksi){
		$query=$this->con->query("delete from transaksi where id_transaksi='$id_transaksi'");
		if ($query === TRUE) {
			$this->alert("Laporan id $id_transaksi telah dihapus");
			$this->redirect("laporan.php");
		}
	}
	function hapus_stok($id_opname){
		$query=$this->con->query("delete from stok_opname where id_opname='$id_opname'");
		if ($query === TRUE) {
			$this->alert("Stok Opname id $id_opname telah dihapus");
			$this->redirect("stok.php");
		}
	}
	// function hapus_pembelian($id_pembelian){
	// 	$query=$this->con->query("delete from pembelian where id_pembelian='$id_pembelian'");
	// 	if ($query === TRUE) {
	// 		$this->alert("pembelian id $id_pembelian telah dihapus");
	// 		$this->redirect("pembelian.php");
	// 	}
	// }
	function hapus_pembelian($no_faktur){
	// Hapus dari detail terlebih dahulu
	$this->con->query("DELETE FROM detail_pembelian WHERE no_faktur_pembelian = '$no_faktur'");

	// Lalu hapus dari tabel pembelian
	$query = $this->con->query("DELETE FROM pembelian WHERE no_faktur_pembelian = '$no_faktur'");

	if ($query === TRUE) {
		$this->alert("Pembelian dengan faktur $no_faktur telah dihapus");
		$this->redirect("pembelian.php");
	}
}

	function hapus_kategori($id_kategori){
		$query=$this->con->query("delete from kategori where id_kategori='$id_kategori'");
		if ($query === TRUE) {
			$this->alert("Kategori id $id_kategori telah dihapus");
			$this->redirect("kategori.php");
		}
	}
	function hapus_barang($id_barang){
		$query=$this->con->query("delete from barang where id_barang='$id_barang'");
		if ($query === TRUE) {
			$this->alert("barang id $id_barang telah dihapus");
			$this->redirect("barang.php");
		}
	}
	function hapus_user($id_user){
		$query=$this->con->query("delete from user where id='$id_user'");
		if ($query === TRUE) {
			$this->alert("Kasir id : $id_user berhasil dihapus");
			$this->redirect("users.php");
		}
	}
	function edit_kategori($id_kategori){
		$query=$this->con->query("select * from kategori where id_kategori='$id_kategori'");
		$data=$query->fetch_assoc();
		return $data;
	}
	function edit_stok($id_opname){
		$query=$this->con->query("select * from stok_opname where id_opname='$id_opname'");
		$data=$query->fetch_assoc();
		return $data;
	}
	function edit_barang($id_barang){
		$query=$this->con->query("select * from barang where id_barang='$id_barang'");
		$data=$query->fetch_assoc();
		return $data;
	}

	function edit_barang_p($id_barang){
		$query=$this->con->query("select * from barang where id_barang='$id_barang'");
		$data=$query->fetch_assoc();
		return $data;
	}

	function aksi_edit_harga_jual_barang($id_barang, $harga_jual) {
    $id_barang   = $this->con->real_escape_string($id_barang);
    $harga_jual  = (int)$harga_jual;

    $sql = "UPDATE barang SET harga_jual = $harga_jual WHERE id_barang = '$id_barang'";
    return $this->con->query($sql);
}


	function edit_barang_produksi($id, $nama, $stok, $harga_beli, $harga_jual, $kategori, $tipe_barang = null) {
    $nama     = $this->con->real_escape_string($nama);
    $stok     = (int)$stok;
    $harga_beli = (int)$harga_beli;
    $harga_jual = (int)$harga_jual;
    $kategori = $this->con->real_escape_string($kategori);

    // Buat SQL update
    $sql = "UPDATE barang SET 
                nama_barang = '$nama', 
                stok = $stok, 
                harga_beli = $harga_beli, 
                harga_jual = $harga_jual, 
                kategori = '$kategori'";

    // Tambahkan jika tipe_barang disediakan
    if (!is_null($tipe_barang)) {
        $tipe_barang = $this->con->real_escape_string($tipe_barang);
        $sql .= ", tipe_barang = '$tipe_barang'";
    }

    $sql .= " WHERE id_barang = '$id'";

    // Eksekusi
    return $this->con->query($sql);
}

	function edit_pembelian($id_pembelian){
		$query=$this->con->query("select * from pembelian where id_pembelian='$id_pembelian'");
		$data=$query->fetch_assoc();
		return $data;
	}
	function edit_distributor($id_distributor){
		$query=$this->con->query("select * from data_distributor where id_distributor='$id_distributor'");
		$data=$query->fetch_assoc();
		return $data;
	}
	function edit_user($id_kasir){
		$query=$this->con->query("select * from user where id='$id_kasir'");
		$data=$query->fetch_assoc();
		return $data;
	}
	function edit_admin(){
		$query=$this->con->query("select * from user where id='1'");
		$data=$query->fetch_assoc();
		return $data;
	}
	function aksi_edit_kategori($id_kategori,$nama_kategori){
		$query=$this->con->query("update kategori set nama_kategori='$nama_kategori' where id_kategori='$id_kategori'");
		 if ($query === TRUE) {
		 	$this->alert("Kategori berhasil di update");
		 	$this->redirect("kategori.php");
		 }else{
		 	$this->alert("Kategori gagal di update");
		 	$this->redirect("kategori.php");

		 }
	}
	function aksi_edit_barang($id_barang,$nama_barang,$stok,$harga_beli,$harga_jual,$id_kategori){
		$query=$this->con->query("update barang set nama_barang='$nama_barang',stok='$stok',harga_beli='$harga_beli',harga_jual='$harga_jual',id_kategori='$id_kategori',date_added=date_added where id_barang='$id_barang'");
		if ($query === TRUE) {
		 	$this->alert("Barang berhasil di update");
		 	$this->redirect("barang.php");
		}
		else{
		 	$this->alert("Barang gagal di update");
		 	$this->redirect("barang.php");
		 }
	}
	function aksi_edit_pembelian($id_pembelian, $no_faktur_pembelian, $distributor, $nama, $id_barang, $jumlah, $harga, $total, $tanggal, $barang_lain = '') {
		// Pastikan koneksi database tersedia
		if (!$this->con) {
			$this->alert("Koneksi database tidak tersedia");
			$this->redirect("pembelian.php");
			return false;
		}
	
		// Persiapan query menggunakan Prepared Statements
		$query = $this->con->prepare("UPDATE pembelian 
									  SET no_faktur_pembelian = ?, 
										  distributor = ?, 
										  nama = ?, 
										  id_barang = ?, 
										  barang_lain = ?, 
										  jumlah = ?, 
										  harga = ?, 
										  total = ?,
										  tanggal = ?
									  WHERE id_pembelian = ?");
	
		if ($query === false) {
			$this->alert("Terjadi kesalahan dalam query: " . $this->con->error);
			$this->redirect("pembelian.php");
			return false;
		}
	
		// Debug: Tampilkan nilai variabel
		/*
		echo "<pre>";
		var_dump(
			$no_faktur_pembelian, 
			$distributor, 
			$nama, 
			$id_barang, 
			$barang_lain, 
			$jumlah, 
			$harga,
			$total,
			$tanggal,
			$id_pembelian
		);
		echo "</pre>";
		*/
	
		// Binding parameter ke dalam query
		// Perhatikan urutan parameter dan tipe data:
		// s = string, i = integer, d = double
		$bound = $query->bind_param("sssisiddsi", 
			$no_faktur_pembelian,  // s
			$distributor,          // s
			$nama,                 // s
			$id_barang,            // i
			$barang_lain,          // s
			$jumlah,               // i
			$harga,                // d
			$total,                // d
			$tanggal,              // s
			$id_pembelian          // i
		);
	
		if ($bound === false) {
			$this->alert("Gagal binding parameter: " . $query->error);
			$this->redirect("pembelian.php");
			return false;
		}
	
		// Eksekusi query
		$executed = $query->execute();
		
		if ($executed) {
			$this->alert("Data pembelian berhasil diperbarui");
			$affected = $query->affected_rows;
			if ($affected === 0) {
				$this->alert("Peringatan: Tidak ada data yang diubah (mungkin data sama dengan yang sudah ada)");
			}
		} else {
			$this->alert("Data pembelian gagal diperbarui: " . $query->error);
		}
	
		// Tutup statement
		$query->close();
	
		// Redirect harus dilakukan setelah semua output selesai
		$this->redirect("pembelian.php");
		
		return $executed;
	}
	
	function aksi_edit_distributor($id_distributor,$nama_pemasok,$alamat,$telp,$nama_penanggung_jawab){
		$query=$this->con->query("update data_distributor set nama_pemasok='$nama_pemasok',alamat='$alamat',telp='$telp',nama_penanggung_jawab='$nama_penanggung_jawab' where id_distributor='$id_distributor'");
		if ($query === TRUE) {
		 	$this->alert("Data berhasil di update");
		 	$this->redirect("distributor.php");
		}
		else{
		 	$this->alert("Data gagal di update");
		 	$this->redirect("distributor.php");
		 }
	}
	// function aksi_edit_user($username,$password,$id){
	// 	if (empty($password)) {
	// 		$query=$this->con->query("update user set username='$username',date_created=date_created where id='$id'");
	// 	}else{
	// 		$password=sha1($password);
	// 		$query=$this->con->query("update user set username='$username',password='$password',date_created=date_created where id='$id'");
	// 	}

	// 	if ($query === TRUE) {
	// 		$this->alert("Kasir berhasil di update");
	// 	 	$this->redirect("users.php");
	// 	}else{
	// 		$this->alert("User gagal di update");
	// 	 	$this->redirect("user.php");
	// 	}
	// }
	function aksi_edit_user($nama, $username, $alamat, $no_tlp, $password, $status, $id){
		if (empty($password)) {
			$query = $this->con->query("UPDATE user SET 
				nama='$nama',
				username='$username',
				alamat='$alamat',
				no_tlp='$no_tlp',
				status='$status',
				date_created=date_created 
				WHERE id='$id'");
		} else {
			$password = sha1($password);
			$query = $this->con->query("UPDATE user SET 
				nama='$nama',
				username='$username',
				alamat='$alamat',
				no_tlp='$no_tlp',
				password='$password',
				status='$status',
				date_created=date_created 
				WHERE id='$id'");
		}
	
		if ($query === TRUE) {
			$this->alert("User berhasil diupdate");
			$this->redirect("users.php");
		} else {
			$this->alert("User gagal diupdate");
			$this->redirect("user.php");
		}
	}
	
	function aksi_edit_admin($username,$password){
		if (empty($password)) {
			$query=$this->con->query("update user set username='$username',date_created=date_created where id='1'");
		}else{
			$password=sha1($password);
			$query=$this->con->query("update user set username='$username',password='$password',date_created=date_created where id='1'");
		}

		if ($query === TRUE) {
			$this->alert("admin berhasil di update, silahkan login kembali");
			session_start();
			session_destroy();
			$this->redirect("index.php");
		}else{
			$this->alert("admin gagal di update");
		 	$this->redirect("user.php");
		}
	}
	function tambah_tempo($id_barang,$jumlah,$trx){
		$q1=$this->con->query("select * from barang where id_barang='$id_barang'");
		$data=$q1->fetch_assoc();
		if ($data['stok'] < $jumlah) {
			$this->alert("stock tidak mencukupi");
			$this->redirect("transaksi.php?action=transaksi_baru");
		}
		else{
			$q=$this->con->query("select * from tempo where id_barang='$id_barang'");
			if ($q->num_rows > 0) {
				$ubah=$q->fetch_assoc();
				$jumbel=$ubah['jumlah_beli']+$jumlah;
				$total_harga=$jumbel*$data['harga_jual'];
				$dbquery=$this->con->query("update tempo set jumlah_beli='$jumbel',total_harga='$total_harga' where id_barang='$id_barang'");
					if ($dbquery === TRUE) {
					$this->con->query("update barang set stok=stok-$jumlah where id_barang='$id_barang'");
					$this->alert("Tersimpan");
					$this->redirect("transaksi.php?action=transaksi_baru");

				}
			}else{
				$total_harga=$jumlah*$data['harga_jual'];
				$query1=$this->con->query("insert into tempo set id_barang='$id_barang',jumlah_beli='$jumlah',total_harga='$total_harga',trx='$trx'");
				if ($query1 === TRUE) {
					$this->con->query("update barang set stok=stok-$jumlah where id_barang='$id_barang'");
					$this->alert("Tersimpan");
					$this->redirect("transaksi.php?action=transaksi_baru");

				}
			}
		}
	}
	function hapus_tempo($id_tempo,$id_barang,$jumbel){
		$query=$this->con->query("delete from tempo where id_subtransaksi='$id_tempo'");
			if ($query===TRUE) {
			$query2=$this->con->query("update barang set stok=stok+$jumbel where id_barang='$id_barang'");
			$this->alert("Barang berhasil dicancel");
			$this->redirect("transaksi.php?action=transaksi_baru");

		}
	}
}
// coded by https://www.athoul.site
$root=new penjualan();
?>
