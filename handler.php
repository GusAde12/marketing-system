<?php
include "root.php";
//require_once 'koneksi.php'; // Gantilah dengan nama file koneksi database Anda

include 'koneksi.php'; // Pastikan ada koneksi ke database


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mencegah akses langsung
if (!isset($_GET['action'])) {
    http_response_code(403);
    exit("No direct script access allowed.");
}


// Menambahkan pembelian
// if ($_GET['action'] == "tambah_pembelian") {
//     // Ambil data dari form POST dan sanitasi inputnya
//     $no_faktur_pembelian = mysqli_real_escape_string($conn, $_POST['no_faktur_pembelian']);
//     $distributor = mysqli_real_escape_string($conn, $_POST['distributor']);
//     $nama = mysqli_real_escape_string($conn, $_POST['nama']);
//     $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
//     $total = (float) $_POST['total']; // Pastikan ini adalah angka dengan format desimal

//     // Panggil fungsi untuk menambahkan data pembelian
//     $root->tambah_pembelian($no_faktur_pembelian, $distributor, $nama, $tanggal, $total);
// }

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == "login") {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
        $root->login($username, $password); // tanpa loginas
    }

    if ($action == "logout") {
        session_start();
        session_destroy();
        $root->redirect("index.php");
    }
	if ($action=="tambah_barang") {
		$root->tambah_barang($_POST['nama_barang'],$_POST['stok'],$_POST['harga_beli'],$_POST['harga_jual'],$_POST['kategori'],$_POST['distributor']);
	}
	if ($action == "tambah_produksi") {
	$tanggal     = $_POST['tanggal'];
	$keterangan  = $_POST['keterangan'];
	$jumlah_jadi = (int) $_POST['jumlah_jadi'];
	$barang_jadi_select = $_POST['id_barang_jadi'];

	// Cek apakah user pilih tambah barang baru
	if ($barang_jadi_select == "new") {
		$nama_barang_jadi = trim($_POST['nama_barang_jadi']);
		// Insert ke tabel barang jadi (tipe_barang = 'jadi')
		$root->con->query("INSERT INTO barang (nama_barang, stok, harga_beli, harga_jual, id_kategori, id_distributor, tipe_barang, date_added)
			VALUES ('$nama_barang_jadi', $jumlah_jadi, 0, 0, 0, 0, 'jadi', NOW())");
		$id_barang_jadi = $root->con->insert_id;
	} else {
		$id_barang_jadi = $barang_jadi_select;
		$root->con->query("UPDATE barang SET stok = stok + $jumlah_jadi WHERE id_barang = '$id_barang_jadi'");
	}

	// Lanjut: insert ke tabel produksi
	$root->con->query("INSERT INTO produksi (tanggal, keterangan) VALUES ('$tanggal', '$keterangan')");
	$id_produksi = $root->con->insert_id;

	// Insert ke detail_produksi dan kurangi stok bahan baku
	foreach ($_POST['id_barang_baku'] as $index => $id_baku) {
		$jumlah_baku = (int) $_POST['jumlah_baku'][$index];
		$root->con->query("INSERT INTO detail_produksi (id_produksi, id_barang_baku, jumlah_baku, id_barang_jadi, jumlah_jadi)
			VALUES ('$id_produksi', '$id_baku', '$jumlah_baku', '$id_barang_jadi', '$jumlah_jadi')");
		$root->con->query("UPDATE barang SET stok = stok - $jumlah_baku WHERE id_barang = '$id_baku'");
	}

	header("Location: barang_produksi.php");
}



	if ($action == "get_barang_by_distributor") {
		$id_distributor = $_GET['id_distributor'];
		
		// Pastikan ID distributor tidak kosong
		if (empty($id_distributor)) {
			echo json_encode(["error" => "ID distributor kosong"]);
			exit;
		}
	
		// Contoh query (pastikan sesuai dengan database Anda)
		$query = "SELECT id_barang, nama_barang FROM barang WHERE id_distributor = ?";
		$stmt = $conn->prepare($query);
		$stmt->bind_param("i", $id_distributor);
		$stmt->execute();
		$result = $stmt->get_result();
	
		// Ambil data
		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
	
		// Jika tidak ada barang
		if (empty($data)) {
			echo json_encode(["error" => "Tidak ada barang ditemukan"]);
			exit;
		}
	
		echo json_encode($data);
	}
	
	
	
	
	
	if ($action == "get_no_faktur") {
		// Format tanggal untuk nomor faktur
		$currentDate = date("Ymd"); // Menyimpan tanggal hari ini (misalnya: 20250401)
	
		// Mencari nomor faktur terakhir dengan format tanggal yang sama
		$query = $root->con->query("SELECT no_faktur_pembelian FROM pembelian WHERE no_faktur_pembelian LIKE 'INV$currentDate%' ORDER BY no_faktur_pembelian DESC LIMIT 1");
		$data = $query->fetch_assoc();
	
		// Menentukan nomor urut berdasarkan nomor faktur terakhir
		if ($data) {
			$lastNoFaktur = $data['no_faktur_pembelian'];
			// Cari posisi tanda '-' dan ambil 4 digit setelahnya
			$dashPos = strpos($lastNoFaktur, '-');
			if ($dashPos !== false) {
				$lastFakturNumber = (int) substr($lastNoFaktur, $dashPos + 1, 4);
				$nextFakturNumber = str_pad($lastFakturNumber + 1, 4, "0", STR_PAD_LEFT);
			} else {
				// Jika format tidak sesuai, mulai dari 0001
				$nextFakturNumber = "0001";
			}
		} else {
			// Jika belum ada data, mulai dari nomor 0001
			$nextFakturNumber = "0001";
		}
	
		// Format nomor faktur baru
		$noFaktur = "INV" . $currentDate . "-" . $nextFakturNumber;
	
		echo $noFaktur;  // Kirimkan nomor faktur baru ke frontend
		exit();
	}
	// if ($action == "tambah_pembelian") {
	// 	$no_faktur = htmlspecialchars(trim($_POST['no_faktur_pembelian']));
	// 	$distributor = htmlspecialchars(trim($_POST['distributor']));
	// 	$nama = ($distributor == "penjual lain") ? htmlspecialchars(trim($_POST['nama'])) : "";
	// 	$barang_lain = ($distributor == "penjual lain") ? htmlspecialchars(trim($_POST['barang_lain'])) : "";
	// 	$id_barang = ($distributor == "penjual lain") ? "" : htmlspecialchars(trim($_POST['barang']));
	// 	$jumlah = (int) htmlspecialchars(trim($_POST['jumlah']));
	// 	$total = (float) htmlspecialchars(trim($_POST['total']));
	// 	$tanggal = htmlspecialchars(trim($_POST['tanggal']));
	
	// 	// Logging untuk debugging
	// 	error_log("Tambah Pembelian: " . json_encode($_POST));
	
	// 	// Validasi nomor faktur
	// 	if (empty($no_faktur)) {
	// 		die(json_encode(["error" => "Nomor faktur tidak valid."]));
	// 	}
	
	// 	// Validasi jumlah dan total harus angka positif
	// 	if ($jumlah <= 0 || $total < 0) {
	// 		die(json_encode(["error" => "Jumlah atau total tidak valid."]));
	// 	}
	
	// 	// Pastikan ID Barang ada jika bukan "penjual lain"
	// 	if ($distributor != "penjual lain" && empty($id_barang)) {
	// 		die(json_encode(["error" => "Barang harus dipilih."]));
	// 	}
	
	// 	// Panggil fungsi tambah pembelian
	// 	$root->tambah_pembelian($no_faktur, $distributor, $nama, $barang_lain, $id_barang, $jumlah, $total, $tanggal);
	
	// 	echo json_encode(["success" => "Pembelian berhasil ditambahkan."]);
	// 	exit;
	// }

	if ($action == "tambah_pembelian") {
    $no_faktur = htmlspecialchars(trim($_POST['no_faktur_pembelian']));
    $distributor = htmlspecialchars(trim($_POST['distributor']));
    $nama = ($distributor == "penjual lain") ? htmlspecialchars(trim($_POST['nama'])) : "";
    $tanggal = htmlspecialchars(trim($_POST['tanggal']));

    $barangs = isset($_POST['barang']) ? $_POST['barang'] : [];
    $barang_lains = isset($_POST['barang_lain']) ? $_POST['barang_lain'] : [];
    $jumlahs = $_POST['jumlah'];
    $hargas = $_POST['harga'];

    $grand_total = 0;
    $jumlah_data = count($jumlahs);

    for ($i = 0; $i < $jumlah_data; $i++) {
        $id_barang = 0;
        $nama_barang_lain = "";

        if ($distributor == "penjual lain") {
            $nama_barang_lain = htmlspecialchars(trim($barang_lains[$i]));
        } else {
            $id_barang = isset($barangs[$i]) ? (int) htmlspecialchars(trim($barangs[$i])) : 0;
        }

        $jumlah = (int) htmlspecialchars(trim($jumlahs[$i]));
        $harga = (float) htmlspecialchars(trim($hargas[$i]));
        $subtotal = $jumlah * $harga;
        $grand_total += $subtotal;

        if ($jumlah <= 0 || $harga < 0) continue;

        $root->tambah_pembelian(
            $no_faktur,
            $distributor,
            $nama,
            $nama_barang_lain,
            $id_barang,
            $jumlah,
            $harga,
            $subtotal,
            $tanggal
        );

        if ($distributor != "penjual lain" && !empty($id_barang)) {
            $stok = $root->get_stok_barang($id_barang);
            $root->update_stok_barang($id_barang, $stok + $jumlah);
        }
    }

    $root->update_total_pembelian($no_faktur, $grand_total);

    // Format response for popup (e.g., SweetAlert)
    echo json_encode([
        "status" => "success",
        "title" => "Berhasil!",
        "message" => "Pembelian dengan No. Faktur <strong>$no_faktur</strong> berhasil ditambahkan.",
        "redirect" => "pembelian.php"
    ]);
    exit;
}




	
	
	
	
	
	
	if ($action=="tambah_distributor") {
		$root->tambah_distributor($_POST['nama_pemasok'],$_POST['alamat'],$_POST['telp'],$_POST['nama_penanggung_jawab']);
	}
	if ($action=="tambah_kategori") {
		$root->tambah_kategori($_POST['nama_kategori']);
	}
	if ($action=="hapus_distributor") {
		$root->hapus_distributor($_GET['id_distributor']);
	}
	if ($action=="hapus_laporan") {
		$root->hapus_laporan($_GET['id_transaksi2']);
	}
	if ($action=="hapus_stok") {
		$root->hapus_stok($_GET['id_opname']);
	}
	// if ($action=="hapus_pembelian") {
	// 	$root->hapus_pembelian($_GET['id_pembelian']);
	// }
	if ($action == "hapus_pembelian") {
    if (isset($_GET['faktur'])) {
        $no_faktur = htmlspecialchars($_GET['faktur']);
        $root->hapus_pembelian($no_faktur);
    } else {
        echo "<script>alert('Faktur tidak ditemukan!'); window.location='pembelian.php';</script>";
    }
}

	if ($action=="hapus_kategori") {
		$root->hapus_kategori($_GET['id_kategori']);
	}
	if ($action=="edit_kategori") {
		$root->aksi_edit_kategori($_POST['id_kategori'],$_POST['nama_kategori']);
	}
	if ($action=="hapus_barang") {
		$root->hapus_barang($_GET['id_barang']);
	}
	if ($action=="edit_barang") {
		$root->aksi_edit_barang($_POST['id_barang'],$_POST['nama_barang'],$_POST['stok'],$_POST['harga_beli'],$_POST['harga_jual'],$_POST['kategori']);
	}
	if ($action == "edit_harga_jual") {
    $id_barang = $_POST['id_barang'];
    $harga_jual = $_POST['harga_jual'];
    
    // Panggil fungsi khusus untuk mengupdate harga jual saja
    $root->aksi_edit_harga_jual_barang($id_barang, $harga_jual);

    // Redirect kembali ke halaman barang produksi setelah update
    header("Location: barang_produksi.php");
    exit;
}


	// if ($action == "edit_pembelian") {
	// 	$root->aksi_edit_pembelian(
	// 		$_POST['id_pembelian'],
	// 		$_POST['no_faktur_pembelian'],
	// 		$_POST['distributor'],
	// 		$_POST['nama'],
	// 		$_POST['barang'],
	// 		$_POST['jumlah'],
	// 		$_POST['harga'],
	// 		$_POST['total'],
	// 		$_POST['tanggal']
	// 	);
	// }
	if ($action == "edit_pembelian") {
		// 1. Validasi method request
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			die("Method request tidak valid");
		}
		
		// 2. Validasi CSRF token (jika digunakan)
		// if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
		//     die("Token keamanan tidak valid");
		// }
	
		// 3. Validasi input wajib
		$required_fields = ['id_pembelian', 'no_faktur_pembelian', 'distributor', 'jumlah', 'harga', 'total', 'tanggal'];
		foreach ($required_fields as $field) {
			if (!isset($_POST[$field]) || empty($_POST[$field])) {
				die("Field $field harus diisi");
			}
		}
	
		// 4. Validasi ID Pembelian 
		if (!is_numeric($_POST['id_pembelian']) || $_POST['id_pembelian'] <= 0) {
			die("ID Pembelian tidak valid");
		}
	
		// 5. Bersihkan input data
		$id_pembelian = (int)$_POST['id_pembelian'];
		$no_faktur = $conn->real_escape_string(htmlspecialchars(strip_tags($_POST['no_faktur_pembelian'])));
		$distributor = $conn->real_escape_string(htmlspecialchars(strip_tags($_POST['distributor'])));
		$barang = isset($_POST['barang']) && is_numeric($_POST['barang']) ? (int)$_POST['barang'] : 0;
		$jumlah = (int)$_POST['jumlah'];
		$harga = (float)str_replace(',', '.', $_POST['harga']); // Handle separator koma
		$total = (float)str_replace(',', '.', $_POST['total']);
		$tanggal = $conn->real_escape_string($_POST['tanggal']);
	
		// 6. Validasi tanggal format
		$date_parts = explode('-', $tanggal);
		if (count($date_parts) != 3 || !checkdate($date_parts[1], $date_parts[2], $date_parts[0])) {
			die("Format tanggal tidak valid (harus YYYY-MM-DD)");
		}
	
		// 7. Validasi numerik nilai
		if ($jumlah <= 0 || $harga <= 0 || $total <= 0) {
			die("Jumlah, harga, dan total harus lebih dari 0");
		}
	
		// 8. Khusus untuk "penjual lain"
		$nama = '';
		$barang_lain = '';
	
		if ($distributor == "penjual lain") {
			if (!isset($_POST['nama']) || empty($_POST['nama'])) {
				die("Nama penjual harus diisi untuk penjual lain");
			}
			if (!isset($_POST['barang_lain']) || empty($_POST['barang_lain'])) {
				die("Nama barang harus diisi untuk penjual lain");
			}
			$nama = $conn->real_escape_string(htmlspecialchars(strip_tags($_POST['nama'])));
			$barang_lain = $conn->real_escape_string(htmlspecialchars(strip_tags($_POST['barang_lain'])));
			$barang = 0; // karena barang dari input manual
		}
	
		// 9. Panggil fungsi untuk edit data
		$result = $root->aksi_edit_pembelian(
			$id_pembelian,
			$no_faktur,
			$distributor,
			$nama,
			$barang,       // id_barang
			$jumlah,
			$harga,
			$total,
			$tanggal,
			$barang_lain   // nama barang dari penjual lain
		);
	
		// 10. Handle hasil eksekusi
	// 	if ($result) {
	// 		$_SESSION['alert'] = "Data pembelian berhasil diperbarui";
	// 		header("Location: pembelian.php?status=sukses");
	// 	} else {
	// 		$_SESSION['alert'] = "Gagal memperbarui data: " . $conn->error;
	// 		header("Location: pembelian.php?status=gagal&id=" . $id_pembelian);
	// 	}
	// 	exit();
	// }
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
	
	if ($action=="edit_distributor") {
		$root->aksi_edit_distributor($_POST['id_distributor'],$_POST['nama_pemasok'],$_POST['alamat'],$_POST['telp'],$_POST['nama_penanggung_jawab']);
	}
	if ($action=="tambah_user") {
		$root->tambah_user($_POST['nama'],$_POST['nama_username'],$_POST['alamat'],$_POST['no_tlp'],$_POST['password'],$_POST['status']);
	}
	if ($action == "tambah_stok") {
		$root->tambah_stok(
			$_POST['id_barang'],
			$_POST['tanggal'],
			$_POST['stok_barang'],
			$_POST['jumlah_asli'],
			$_POST['selisih'],
			$_POST['keterangan']
		);
	}
	
	if ($action=="hapus_user") {
		$root->hapus_user($_GET['id_user']);
	}
	if ($action=="edit_user") {
		$root->aksi_edit_user($_POST['nama'],$_POST['nama_username'],$_POST['alamat'],$_POST['no_tlp'],$_POST['password'],$_POST['status'],$_POST['id']);
	}
	if ($action=="edit_admin") {
		$root->aksi_edit_admin($_POST['username'],$_POST['password']);
	}
	if ($action=="reset_admin") {
		$pass=sha1("admin");
		$q=$root->con->query("update user set username='admin',password='$pass',date_created=date_created where id='1'");
		if ($q === TRUE) {
			$root->alert("admin berhasil direset, username & password = 'admin'");
			session_start();
			session_destroy();
			$root->redirect("index.php");
		}
	}
	if ($action=="tambah_tempo") {
		$root->tambah_tempo($_POST['id_barang'],$_POST['jumlah'],$_POST['trx']);
	}
	if ($action=="hapus_tempo") {
		$root->hapus_tempo($_GET['id_tempo'],$_GET['id_barang'],$_GET['jumbel']);
	}
	// if ($action=="selesai_transaksi") {
	// 	session_start();
	// 	$trx=date("d")."/AF/".$_SESSION['id']."/".date("y/h/i/s");

	// 		$query=$root->con->query("insert into transaksi set kode_kasir='$_SESSION[id]',total_bayar='$_POST[total_bayar]',no_invoice='$trx',nama_pembeli='$_POST[nama_pembeli]'");

	// 	$trx2=date("d")."/AF/".$_SESSION['id']."/".date("y");
	// 	$get1=$root->con->query("select *  from transaksi where no_invoice='$trx'");
	// 	$datatrx=$get1->fetch_assoc();
	// 	$id_transaksi2=$datatrx['id_transaksi'];

	// 	$query2=$root->con->query("select * from tempo where trx='$trx2'");
	// 	while ($f=$query2->fetch_assoc()) {
	// 		$root->con->query("insert into sub_transaksi set id_barang='$f[id_barang]',id_transaksi='$id_transaksi2',jumlah_beli='$f[jumlah_beli]',total_harga='$f[total_harga]',no_invoice='$trx'");
	// 	}
	// 	$root->con->query("delete from tempo where trx='$trx2'");
	// 	$root->alert("Transaksi berhasil");
	// 	$root->redirect("transaksi.php");


	// }
	if ($action=="selesai_transaksi") {
		session_start();
		$trx = date("d") . "/AF/" . $_SESSION['id'] . "/" . date("y/h/i/s");
	
		$status_bayar = $_POST['status_bayar'];
		$jumlah_dp = isset($_POST['jumlah_dp']) ? $_POST['jumlah_dp'] : 0;
		$sisa_hutang = isset($_POST['sisa_hutang']) ? $_POST['sisa_hutang'] : 0;

		if ($jumlah_dp > $_POST['total_bayar']) {
			$root->alert("Jumlah DP tidak boleh lebih dari total bayar!");
			$root->redirect("transaksi.php?action=transaksi_baru");
			exit;
		}
		
	
		$query = $root->con->query("INSERT INTO transaksi 
			SET kode_kasir='$_SESSION[id]',
				total_bayar='$_POST[total_bayar]',
				no_invoice='$trx',
				nama_pembeli='$_POST[nama_pembeli]',
				status_bayar='$status_bayar',
				jumlah_dp='$jumlah_dp',
				sisa_hutang='$sisa_hutang'");
	
		$trx2 = date("d") . "/AF/" . $_SESSION['id'] . "/" . date("y");
		$get1 = $root->con->query("SELECT * FROM transaksi WHERE no_invoice='$trx'");
		$datatrx = $get1->fetch_assoc();
		$id_transaksi2 = $datatrx['id_transaksi'];
	
		$query2 = $root->con->query("SELECT * FROM tempo WHERE trx='$trx2'");
		while ($f = $query2->fetch_assoc()) {
			$root->con->query("INSERT INTO sub_transaksi 
				SET id_barang='$f[id_barang]',
					id_transaksi='$id_transaksi2',
					jumlah_beli='$f[jumlah_beli]',
					total_harga='$f[total_harga]',
					no_invoice='$trx'");
		}
	
		$root->con->query("DELETE FROM tempo WHERE trx='$trx2'");
		$root->alert("Transaksi berhasil");
		$root->redirect("transaksi.php");
	}
	
	if ($action=="delete_transaksi") {
		$q1=$root->con->query("delete from transaksi where id_transaksi='$_GET[id]'");
		$q2=$root->con->query("delete from sub_transaksi where id_transaksi='$_GET[id]'");
		if ($q1===TRUE && $q2 === TRUE) {
			$root->alert("Transaksi No $_GET[id] Berhasil Dihapus");
			$root->redirect("laporan.php");
		}
	}


}else{
	echo "no direct script are allowed";
}
?>
