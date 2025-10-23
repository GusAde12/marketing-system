<?php include "head.php" ?>
<?php
    if (isset($_GET['action']) && $_GET['action']=="detail_transaksi") {
        include "detail_transaksi.php";
    }
    else {
?>
<script type="text/javascript">
    document.title="Laporan Penjualan";
    document.getElementById('laporan').classList.add('active');
</script>

<div class="content">
    <div class="padding">
        <div class="bgwhite" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="padding">
                <div class="contenttop">
                    <div class="left">
                        <h3 class="jdl" style="margin: 0; color: #2d3748; font-weight: 600;">Laporan Penjualan</h3>
                    </div>
                    <div class="right">
                        <script type="text/javascript">
                            function gotojenis(val){
                                var value=val.options[val.selectedIndex].value;
                                window.location.href="laporan.php?jenis="+value+"";
                            }
                            function gotofilter(val){
                                var value=val.options[val.selectedIndex].value;
                                window.location.href="laporan.php?jenis=<?php if (isset($_GET['jenis'])) { echo $_GET['jenis']; } ?>&filter_record="+value;
                            }
                        </script>
                        <span style="float: left; padding: 8px 10px; margin-right: 10px; color: #4a5568;">Filter dan cetak :</span>
                        <form action="cetak_laporan.php" style="display: inline-flex; gap: 8px;" target="_blank" method="post">
                            <select class="form-select" onchange="gotojenis(this)" name="jenis_laporan" required style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px;">
                                <option>Pilih Jenis</option>
                                <option value="perhari" <?php if (isset($_GET['jenis'])&&$_GET['jenis']=='perhari'){ echo "selected"; } ?>>Perhari</option>
                                <option value="perbulan" <?php if (isset($_GET['jenis'])&&$_GET['jenis']=='perbulan'){ echo "selected"; } ?>>Perbulan</option>
                            </select>
                            <select class="form-select" onchange="gotofilter(this)" required name="tgl_laporan" style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px;">
                                <?php
                                    if (isset($_GET['jenis'])&&$_GET['jenis']=='perhari') {
                                        ?>
                                        <option>Pilih Hari</option>
                                        <?php
                                        $data=$root->con->query("select distinct date(tgl_transaksi) as tgl_transaksi from transaksi order by id_transaksi desc");
                                        while ($f=$data->fetch_assoc()) {
                                            ?>
                                                <option <?php if (isset($_GET['filter_record'])) { if ($_GET['filter_record'] == date('d-m-Y',strtotime($f['tgl_transaksi']))) { echo "selected"; } } ?> value="<?= date('d-m-Y',strtotime($f['tgl_transaksi'])) ?>"><?= date('d-m-Y',strtotime($f['tgl_transaksi'])) ?></option>
                                            <?php
                                        }
                                    }else if(isset($_GET['jenis'])&&$_GET['jenis']=='perbulan') {
                                ?>
                                <option value="">Pilih Bulan</option>
                                <?php
                                    $data=$root->con->query("select distinct EXTRACT(YEAR FROM tgl_transaksi) AS OrderYear,EXTRACT(MONTH FROM tgl_transaksi) AS OrderMonth from transaksi order by id_transaksi desc");
                                    while ($f=$data->fetch_assoc()) {
                                        ?>
                                            <option <?php if (isset($_GET['filter_record'])) { 
                                                if($f['OrderMonth']<=9){
                                                $aaaa="0".$f['OrderMonth']."-".$f['OrderYear'];
                                            }else{
                                                $aaaa=$f['OrderMonth']."-".$f['OrderYear'];
                                            }
                                                if ($_GET['filter_record'] == $aaaa) { 
                                                    echo "selected"; } } ?> 
                                            value="<?php 
                                            if($f['OrderMonth']<=9){
                                                echo "0".$f['OrderMonth']."-".$f['OrderYear'];
                                            }else{
                                                echo $f['OrderMonth']."-".$f['OrderYear'];
                                            } ?>"><?php 
                                            if($f['OrderMonth']<=9){
                                                echo "0".$f['OrderMonth']."-".$f['OrderYear'];
                                            }else{
                                                echo $f['OrderMonth']."-".$f['OrderYear'];
                                            }
                                            ?></option>
                                        <?php
                                    }
                                    }else{
                                        echo "<option>Pilih Jenis Cetak terlebih dahulu</option>";
                                    }
                                ?>
                            </select>
                            <button class="btnblue" style="padding: 8px 20px; border-radius: 4px; font-weight: 500;" <?php if (isset($_GET['filter_record'])) {}else{ ?> disabled="disabled" title="Pilih jenis dan tanggal lebih dulu"<?php } ?>>
                                <i class="fa fa-print" style="margin-right: 8px;"></i> Cetak
                            </button>
                        </form>
                    </div>
                    <div class="both"></div>
                </div>
                
                <div class="table-responsive" style="overflow-x: auto; margin-top: 20px;">
                    <table class="datatable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; color: #495057;">
                                <th width="35px" style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6;">#</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">No Invoice</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">User</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Pembeli</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Tanggal Transaksi</th>
                                <th style="padding: 12px 15px; text-align: right; border-bottom: 2px solid #dee2e6;">Total Bayar</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6;">Status Bayar</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['filter_record'])) {
                                if ($_GET['jenis']=='perhari') {
                                    $aksi1=1;
                                }else{
                                    $aksi1=2;
                                }
                                $root->filter_tampil_laporan($_GET['filter_record'],$aksi1);
                            }else{
                            $root->tampil_laporan();
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
    
    .btn-delete {
        background-color: #dc3545;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-lunas {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-dp {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-hutang {
        background-color: #f8d7da;
        color: #721c24;
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
    
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 1em;
    }
    
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 8px;
    }
</style>

<?php 
}
include "foot.php" ?>