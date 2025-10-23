<?php
include "head.php";
session_start();
include "koneksi.php"; // atau file koneksi yang sesuai

$id_transaksi = $_GET['id_transaksi'];

$get = $root->con->query("SELECT * FROM transaksi WHERE id_transaksi='$id_transaksi'");
$data = $get->fetch_assoc();

if (isset($_POST['submit'])) {
    $bayar = $_POST['bayar_berikutnya'];
    $jumlah_dp_baru = $data['jumlah_dp'] + $bayar;
    $sisa_hutang_baru = $data['total_bayar'] - $jumlah_dp_baru;

    $status_bayar_baru = ($sisa_hutang_baru <= 0) ? 'Lunas' : 'DP';

    // Simpan ke tabel transaksi (update)
    $update = $root->con->query("
        UPDATE transaksi SET 
            jumlah_dp='$jumlah_dp_baru',
            sisa_hutang='$sisa_hutang_baru',
            status_bayar='$status_bayar_baru'
        WHERE id_transaksi='$id_transaksi'
    ");

    // Simpan ke tabel pembayaran (insert log pembayaran)
    $insert_log = $root->con->query("
        INSERT INTO pembayaran (id_transaksi, jumlah_bayar, tanggal_bayar, keterangan)
        VALUES ('$id_transaksi', '$bayar', NOW(), 'Pembayaran Cicilan')
    ");

    if ($update && $insert_log) {
        $root->alert("Pembayaran berhasil disimpan!");
        $root->redirect("transaksi.php");
    } else {
        echo "<div class='error'>Gagal menyimpan pembayaran atau log.</div>";
    }
}
?>

<div class="content">
    <div class="padding">
        <div class="bgwhite">
            <div class="padding">
                <h3>Pembayaran Berikutnya</h3>
                <table>
                    <tr><td><b>No Invoice</b></td><td>: <?= $data['no_invoice'] ?></td></tr>
                    <tr><td><b>Nama Pembeli</b></td><td>: <?= $data['nama_pembeli'] ?></td></tr>
                    <tr><td><b>Total Bayar</b></td><td>: Rp <?= number_format($data['total_bayar']) ?></td></tr>
                    <tr><td><b>Jumlah DP</b></td><td>: Rp <?= number_format($data['jumlah_dp']) ?></td></tr>
                    <tr><td><b>Sisa Hutang</b></td><td>: Rp <?= number_format($data['sisa_hutang']) ?></td></tr>
                    <tr><td><b>Status Bayar</b></td><td>: <?= $data['status_bayar'] ?></td></tr>
                </table>
                <br>
                <?php if ($data['status_bayar'] != "Lunas") { ?>

                <?php if (isset($_SESSION['status']) && $_SESSION['status'] == 1) { ?>
                    <form method="POST" class="payment-form">
                    <div class="form-group">
                        <label class="payment-label"><i class="fas fa-money-bill-wave"></i> <strong>BAYAR SEKARANG</strong></label>
                        <div class="input-group payment-input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" name="bayar_berikutnya" required min="1" max="<?= $data['sisa_hutang'] ?>" 
                                class="form-control payment-input" placeholder="Masukkan jumlah pembayaran">
                        </div>
                        <small class="form-text text-muted">Sisa hutang: Rp <?= number_format($data['sisa_hutang']) ?></small>
                    </div>
                    
                    <div class="payment-action-buttons">
                        <button type="submit" name="submit" class="btn btn-payment-confirm">
                            <i class="fas fa-check-circle"></i> KONFIRMASI PEMBAYARAN
                        </button>
                        <a href="transaksi.php" class="btn btn-payment-cancel">
                            <i class="fas fa-times-circle"></i> BATAL
                        </a>
                    </div>
                </form>

                <?php } else { ?>
                    <!-- ALERT UNTUK NON-ADMIN -->
                    <div class="alert alert-warning mt-4" role="alert">
                        <strong>Perhatian:</strong> Anda tidak memiliki akses untuk melakukan pembayaran. Silakan hubungi admin.
                    </div>
                <?php } ?>

                <?php } else { ?>
                    <div class="payment-complete-alert">
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading"><i class="fas fa-check-circle"></i> TRANSAKSI SUDAH LUNAS</h4>
                            <p>Semua pembayaran untuk transaksi ini telah dilunasi.</p>
                            <hr>
                            <!-- <a href="transaksi.php" class="btn btn-payment-back">
                                <i class="fas fa-arrow-left"></i> KEMBALI KE DAFTAR TRANSAKSI
                            </a> -->
                        </div>
                    </div>
                <?php } ?>
                <br>

                <hr>
                <h4>Riwayat Pembayaran:</h4>
                <table class="datatable">
                <thead>
                    <tr>
                    <!-- <th>No</th> -->
                    <th>Tanggal Bayar</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $histori = $root->con->query("SELECT * FROM pembayaran WHERE id_transaksi='$id_transaksi' ORDER BY tanggal_bayar ASC");
                    if ($histori->num_rows > 0) {
                        while ($r = $histori->fetch_assoc()) {
                            echo "<tr>
                                    
                                    <td>" . date("d-m-Y H:i", strtotime($r['tanggal_bayar'])) . "</td>
                                    <td>Rp. " . number_format($r['jumlah_bayar']) . "</td>
                                    <td>{$r['keterangan']}</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Belum ada pembayaran sebelumnya.</td></tr>";
                    }
                    ?>
                </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<style>
/* Style untuk form pembayaran */
.payment-form {
    max-width: 500px;
    margin: 0 auto;
    padding: 25px;
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.payment-label {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 15px;
    display: block;
}

.payment-input-group {
    margin-bottom: 10px;
}

.payment-input {
    height: 45px;
    font-size: 1.1rem;
    border-right: none;
}

.payment-input:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Style untuk tombol */
.btn-payment-confirm {
    background-color: #28a745;
    color: white;
    padding: 12px 25px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 5px;
    border: none;
    transition: all 0.3s;
    margin-right: 10px;
}

.btn-payment-confirm:hover {
    background-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn-payment-cancel {
    background-color: #dc3545;
    color: white;
    padding: 12px 25px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 5px;
    transition: all 0.3s;
}

.btn-payment-cancel:hover {
    background-color: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn-payment-back {
    background-color: #17a2b8;
    color: white;
    padding: 10px 20px;
    font-size: 0.9rem;
    border-radius: 5px;
    transition: all 0.3s;
}

.btn-payment-back:hover {
    background-color: #138496;
    text-decoration: none;
    transform: translateY(-2px);
}

/* Style untuk alert transaksi lunas */
.payment-complete-alert {
    max-width: 1500px;
    margin: 0 auto;
}

.payment-complete-alert .alert {
    border-radius: 10px;
}
</style>

<?php include "foot.php"; ?>
