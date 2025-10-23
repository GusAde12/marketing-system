<?php include "head.php" ?>
<?php
    if (isset($_GET['action']) && $_GET['action']=="produksi_barang") {
        include "tambah_produksi_barang.php"; // form produksi
    } else if (isset($_GET['action']) && $_GET['action']=="edit_barang_p") {
        include "edit_barang_produksi.php";
    } else {
?>

<script type="text/javascript">
    document.title="Data Produksi";
    document.getElementById('barang_produksi').classList.add('active');
</script>

<div class="content">
    <div class="padding">
        <div class="bgwhite" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="padding">
                <div class="contenttop">
                    <div class="left">
                        <a href="?action=produksi_barang" class="btnblue" style="padding: 10px 20px; border-radius: 4px; font-weight: 500;">
                            <i class="fa fa-plus" style="margin-right: 8px;"></i> Tambah Produksi
                        </a>
                    </div>
                    <div class="both"></div>
                </div>

                <div class="datainfo" style="margin: 20px 0;">
                    <span class="label" style="font-size: 14px; color: #555;">
                        Total Produksi: <strong>
                        <?php 
                            $result = $root->con->query("SELECT COUNT(*) as jumlah FROM produksi");
                            $row = $result->fetch_assoc();
                            echo $row['jumlah'];
                        ?>
                        </strong>
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="datatable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="padding: 12px;">#</th>
                                <th style="padding: 12px;">Nama Barang</th>
                                <th style="padding: 12px;">Jumlah Stok</th>
                                <th style="padding: 12px;">Tanggal</th>
                                <th style="padding: 12px;">Keterangan</th>
                                <th style="padding: 12px;">Harga Jual</th>
                                
                                <!-- <th style="padding: 12px;">Jumlah Jadi</th> -->
                                <th style="padding: 12px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$query = $root->con->query("
    SELECT 
    b.id_barang,
    b.nama_barang,
    b.stok,
    b.harga_jual,               
    p.id_produksi,
    p.tanggal,
    p.keterangan,
    dp.jumlah_jadi
FROM barang b
LEFT JOIN (
    SELECT dp.id_barang_jadi, dp.id_produksi, dp.jumlah_jadi
    FROM detail_produksi dp
    INNER JOIN (
        SELECT id_barang_jadi, MAX(id_produksi) as max_id
        FROM detail_produksi
        GROUP BY id_barang_jadi
    ) last_dp ON dp.id_barang_jadi = last_dp.id_barang_jadi AND dp.id_produksi = last_dp.max_id
    GROUP BY dp.id_barang_jadi
) dp ON b.id_barang = dp.id_barang_jadi
LEFT JOIN produksi p ON p.id_produksi = dp.id_produksi
WHERE b.tipe_barang = 'jadi'
ORDER BY b.nama_barang ASC

");


$no = 1;
while ($row = $query->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$no}</td>";
    echo "<td>" . $row['nama_barang'] . "</td>";
    echo "<td>" . $row['stok'] . "</td>";
    echo "<td>" . (isset($row['tanggal']) ? $row['tanggal'] : '-') . "</td>";
    echo "<td>" . (isset($row['keterangan']) ? $row['keterangan'] : '-') . "</td>";
    echo "<td>" . (isset($row['harga_jual']) ? "Rp. " . number_format($row['harga_jual'], 0, ',', '.') : '-') . "</td>";
    
    echo "<td style='text-align:center;'>";
    if (!empty($row['id_barang'])) {
        echo "<a href='?action=edit_barang_p&id_barang={$row['id_barang']}' class='btn-action btn-edit' title='Edit'><i class='fa fa-edit'></i></a>
              <a href='hapus_produksi.php?id_barang={$row['id_barang']}' onclick=\"return confirm('Hapus data ini?')\" class='btn-action btn-delete' title='Hapus'><i class='fa fa-trash'></i></a>";
    } else {
        echo "-";
    }
    echo "</td>";
    echo "</tr>";
    $no++;
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
    .btn-edit {
        background-color: #17a2b8;
    }
    .btn-delete {
        background-color: #dc3545;
    }
    .btnblue {
        background-color: #007bff;
        color: white;
        transition: 0.2s;
    }
    .btnblue:hover {
        background-color: #0069d9;
    }
</style>

<?php } include "foot.php" ?>
