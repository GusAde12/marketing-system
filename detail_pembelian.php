<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "koneksi.php";

// Ambil data berdasarkan no_faktur_pembelian
if (!isset($_GET['faktur'])) {
    die("Faktur tidak ditemukan.");
}
$no_faktur = $_GET['faktur'];

// Ambil data utama pembelian (header)
$query = $conn->prepare("SELECT * FROM pembelian WHERE no_faktur_pembelian = ?");
$query->bind_param("s", $no_faktur);
$query->execute();
$result = $query->get_result();
$pembelian = $result->fetch_assoc();

if (!$pembelian) {
    die("Data pembelian tidak ditemukan.");
}

// Ambil semua detail pembelian dari tabel detail_pembelian (termasuk penjual lain)
$detail = [];
$query_detail = $conn->prepare("
    SELECT d.id_barang, d.barang_lain, d.jumlah, d.harga, d.total, b.nama_barang 
    FROM detail_pembelian d
    LEFT JOIN barang b ON d.id_barang = b.id_barang
    WHERE d.no_faktur_pembelian = ?
");
$query_detail->bind_param("s", $no_faktur);
$query_detail->execute();
$detail_result = $query_detail->get_result();
while ($row = $detail_result->fetch_assoc()) {
    $detail[] = $row;
}

?>

<script type="text/javascript">
    document.title = "Detail Pembelian";
    document.getElementById('pembelian').classList.add('active');
</script>

<div class="content">
    <div class="padding">
        <div class="bgwhite">
            <div class="padding">
                <div class="detail-pembelian-container">
                    <h3 class="detail-title"><i class="fa fa-file-text-o"></i> Detail Pembelian</h3>

                    <a href="cetak_detail_pembelian.php?faktur=<?= urlencode($pembelian['no_faktur_pembelian']) ?>" class="btn btn-primary" target="_blank">
                        <i class="fa fa-print"></i> Cetak Faktur
                    </a>

                    
                    <div class="detail-grid">
                        <div class="detail-label">No Faktur</div>
                        <div class="detail-value"><span class="badge-faktur"><?= htmlspecialchars($pembelian['no_faktur_pembelian']) ?></span></div>

                        <div class="detail-label">Distributor</div>
                        <div class="detail-value">
                            <?= ($pembelian['distributor'] == 'penjual lain') 
                                ? '<span class="badge-penjual-lain">'.htmlspecialchars($pembelian['distributor']).'</span>'
                                : '<span class="badge-distributor">'.htmlspecialchars($pembelian['distributor']).'</span>'; ?>
                        </div>

                        <?php if (!empty($pembelian['nama'])): ?>
                        <div class="detail-label">Nama Penjual</div>
                        <div class="detail-value"><?= htmlspecialchars($pembelian['nama']) ?></div>
                        <?php endif; ?>

                        <div class="detail-label">Tanggal</div>
                        <div class="detail-value">
                            <i class="fa fa-calendar"></i> <?= $pembelian['tanggal'] != '0000-00-00' ? date("d/m/Y", strtotime($pembelian['tanggal'])) : '-' ?>
                        </div>
                    </div>

                    <hr style="margin: 20px 0; border: 1px dashed #ccc;">

                    <h4 style="margin-bottom: 10px;">Detail Barang:</h4>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f5f5f5;">
                                    <th style="padding: 10px; border: 1px solid #ddd;">No</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Nama Barang</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Jumlah</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Harga Satuan</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($detail)): 
                                $no = 1;
                                $total_final = 0;
                                foreach ($detail as $row): 
                                    $total_final += $row['total'];
                                ?>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $no++ ?></td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <?= !empty($row['nama_barang']) ? htmlspecialchars($row['nama_barang']) : htmlspecialchars($row['barang_lain']) ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <?= isset($row['jumlah']) ? htmlspecialchars($row['jumlah']) . ' pcs' : '-' ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        Rp <?= isset($row['harga']) ? number_format($row['harga'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        Rp <?= number_format($row['total'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="5" style="text-align:center; padding: 20px;">Tidak ada detail barang ditemukan.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="detail-total" style="margin-top: 20px; text-align: right;">
                        <strong>Total Pembelian:</strong> 
                        <span class="total">Rp <?php echo number_format(isset($total_final) ? $total_final : 0, 0, ',', '.'); ?></span>
                    </div>

                    <div class="detail-actions">
                        <a href="pembelian.php" class="btn btn-back">
                            <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .detail-pembelian-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .detail-title {
        color: #333;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    .detail-grid {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 12px 15px;
        margin-bottom: 25px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #555;
        align-self: center;
    }
    
    .detail-value {
        padding: 8px 0;
    }
    
    .badge-faktur, 
    .badge-distributor,
    .badge-penjual-lain {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.85em;
    }
    
    .badge-faktur {
        background-color: #e3f2fd;
        color: #1976d2;
        border: 1px solid #bbdefb;
    }
    
    .badge-distributor {
        background-color: #e8f5e9;
        color: #388e3c;
        border: 1px solid #c8e6c9;
    }
    
    .badge-penjual-lain {
        background-color: #fff3e0;
        color: #ff6d00;
        border: 1px solid #ffe0b2;
    }
    
    .total {
        color: #2e7d32;
        font-weight: 600;
        font-size: 1.1em;
    }
    
    .detail-actions {
        border-top: 1px solid #eee;
        padding-top: 20px;
        text-align: right;
    }
    
    .btn-back {
        background-color: #f5f5f5;
        color: #333;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }
    
    .btn-back:hover {
        background-color: #e0e0e0;
        color: #000;
    }

    .fa {
        margin-right: 5px;
        width: 16px;
        text-align: center;
    }
</style>
