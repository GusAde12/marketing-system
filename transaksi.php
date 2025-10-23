<?php include "head.php" ?>
<?php
    if (isset($_GET['action']) && $_GET['action']=="transaksi_baru") {
        include "transaksi_baru.php";
    }
    else if (isset($_GET['action']) && $_GET['action']=="detail_transaksi") {
        include "detail_transaksi.php";
    }
    else {
?>
<script type="text/javascript">
    document.title = "Transaksi";
    document.getElementById('transaksi').classList.add('active');
</script>
<div class="content">
    <div class="padding">
        <div class="bgwhite" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="padding">
                <div class="contenttop">
                    <div class="left">
                        <a href="?action=transaksi_baru" class="btnblue" style="padding: 10px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center;">
                            <i class="fa fa-plus-circle" style="margin-right: 8px;"></i> Transaksi Baru
                        </a>
                    </div>
                    <div class="both"></div>
                </div>
                
                <div class="datainfo" style="margin: 20px 0; display: flex; justify-content: space-between; align-items: center;">
                    <span class="label" style="font-size: 14px; color: #555;">Jumlah Transaksi: <strong><?= $root->show_jumlah_trans() ?></strong></span>
                    <div class="searchbox" style="position: relative;">
                        <form method="get" style="display: inline;">
                            <input type="text" name="cari" placeholder="Cari transaksi..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>" style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; width: 250px;">
                            <button type="submit" style="display:none;"></button>
                            <i class="fa fa-search" style="position: absolute; right: 15px; top: 10px; color: #999;"></i>
                        </form>
                    </div>

                </div>
                
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="datatable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; color: #495057;">
                                <th width="35px" style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">NO</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Tanggal Transaksi</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Status Bayar</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Jumlah DP</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Sisa Piutang</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Total Bayar</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Nama Pembeli</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">No Invoice</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6; width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $cari = isset($_GET['cari']) ? $root->con->real_escape_string($_GET['cari']) : '';
                            $where = "WHERE kode_kasir='$_SESSION[id]'";

                            if (!empty($cari)) {
                                $where .= " AND (
                                    nama_pembeli LIKE '%$cari%' OR 
                                    no_invoice LIKE '%$cari%' OR 
                                    status_bayar LIKE '%$cari%' OR 
                                    total_bayar LIKE '%$cari%'
                                )";
                            }

                            $q = $root->con->query("SELECT * FROM transaksi $where ORDER BY id_transaksi DESC");

                            if ($q->num_rows > 0) {
                                while ($f = $q->fetch_assoc()) {
                                    $statusClass = strtolower($f['status_bayar']) == 'lunas' ? 'status-success' : 'status-warning';
                            ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px 15px;"><?= $no++ ?></td>
                                        <td style="padding: 12px 15px;"><?= date("d-m-Y", strtotime($f['tgl_transaksi'])) ?></td>
                                        <td style="padding: 12px 15px;">
                                            <span class="status-badge <?= $statusClass ?>" style="padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                                <?= $f['status_bayar'] ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px 15px;">Rp. <?= number_format($f['jumlah_dp']) ?></td>
                                        <td style="padding: 12px 15px;">Rp. <?= number_format($f['sisa_hutang']) ?></td>
                                        <td style="padding: 12px 15px;">Rp. <?= number_format($f['total_bayar']) ?></td>
                                        <td style="padding: 12px 15px;"><?= $f['nama_pembeli'] ?></td>
                                        <td style="padding: 12px 15px; font-family: monospace;"><?= $f['no_invoice'] ?></td>
                                        <td style="padding: 12px 15px; text-align: center;">
                                            <div class="action-buttons" style="display: flex; justify-content: center; gap: 8px;">
                                                <a href="?action=detail_transaksi&id_transaksi=<?= $f['id_transaksi'] ?>" class="btn-action btn-view" title="Detail">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="cetak_nota.php?oid=<?= base64_encode($f['id_transaksi']) ?>&id-uid=<?= base64_encode($f['nama_pembeli']) ?>&inf=<?= base64_encode($f['no_invoice']) ?>&tb=<?= base64_encode($f['total_bayar']) ?>&uuid=<?= base64_encode(date("d-m-Y", strtotime($f['tgl_transaksi']))) ?>" target="_blank" class="btn-action btn-print" title="Cetak">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                                <a href="bayar_berikutnya.php?id_transaksi=<?= $f['id_transaksi'] ?>" class="btn-action btn-history" title="Riwayat">
                                                    <i class="fa fa-history"></i>
                                                </a>
                                                <?php if (strtolower($f['status_bayar']) == 'dp') { ?>
                                                    <a href="bayar_berikutnya.php?id_transaksi=<?= $f['id_transaksi'] ?>" class="btn-action btn-pay" title="Bayar">
                                                        <i class="fa fa-credit-card"></i>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td style="padding: 20px; text-align: center; color: #999;" colspan="9">
                                        <i class="fa fa-info-circle" style="margin-right: 8px;"></i> Belum ada transaksi
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        color: white;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .btn-view {
        background-color: #17a2b8;
    }
    
    .btn-print {
        background-color: #6c757d;
    }
    
    .btn-history {
        background-color: #ffc107;
    }
    
    .btn-pay {
        background-color: #28a745;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .datatable tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btnblue {
        background-color: #007bff;
        color: white;
        transition: all 0.2s ease;
    }
    
    .btnblue:hover {
        background-color: #0069d9;
    }
</style>

<?php 
}
include "foot.php" ?>