<?php include "head.php" ?>
<?php
    if (isset($_GET['action']) && $_GET['action']=="tambah_barang") {
        include "tambah_barang.php";
    }
    else if (isset($_GET['action']) && $_GET['action']=="edit_barang") {
        include "edit_barang.php";
    }
    else{
?>
<script type="text/javascript">
    document.title="Barang";
    document.getElementById('barang').classList.add('active');
</script>
<script type="text/javascript" src="assets/jquery.tablesorter.min.js"></script>
<script type="text/javascript">
    $(function(){
        $.tablesorter.addWidget({
            id:"indexFirstColumn",
            format:function(table){
                $(table).find("tr td:first-child").each(function(index){
                    $(this).text(index+1);
                })
            }
        });
        $("table").tablesorter({
            widgets:['indexFirstColumn'],
            headers:{
                0:{sorter:false},
                7:{sorter:false},
            }
        });
    });
</script>
<div class="content">
    <div class="padding">
        <div class="bgwhite" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="padding">
                <div class="contenttop">
                    <div class="left">
                        <a href="?action=tambah_barang" class="btnblue" style="padding: 10px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center; margin-right: 10px;">
                            <i class="fa fa-plus" style="margin-right: 8px;"></i> Tambah Barang
                        </a>
                        <a href="cetak_barang.php" class="btnblue" target="_blank" style="padding: 10px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center;">
                            <i class="fa fa-print" style="margin-right: 8px;"></i> Cetak
                        </a>
                    </div>
                    <div class="right">
                        <script type="text/javascript">
                            function gotocat(val){
                                var value=val.options[val.selectedIndex].value;
                                window.location.href="barang.php?id_cat="+value+"";
                            }
                        </script>
                        <select class="form-select" onchange="gotocat(this)" style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; margin-right: 10px;">
                            <option value="">Filter kategori</option>
                            <?php
                                $data=$root->con->query("select * from kategori");
                                while ($f=$data->fetch_assoc()) {
                                    ?>
                                        <option <?php if (isset($_GET['id_cat'])) { if ($_GET['id_cat'] == $f['id_kategori']) { echo "selected"; } } ?> value="<?= $f['id_kategori'] ?>"><?= $f['nama_kategori'] ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                        <form class="search-form">
                            <input type="search" name="q" placeholder="Cari Barang..." value="<?php echo $keyword=isset($_GET['q'])?$_GET['q']:""; ?>" style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; width: 200px;">
                            <button type="submit" style="background: none; border: none; margin-left: -30px; color: #6c757d;">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="both"></div>
                </div>
                
                <div class="datainfo" style="margin: 20px 0;">
                    <span class="label" style="font-size: 14px; color: #555;">Jumlah Barang: <strong><?= $root->show_jumlah_barang() ?></strong></span>
                </div>
                
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="datatable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; color: #495057;">
                                <th width="35px" style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">#</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; cursor: pointer;">Nama Barang <i class="fa fa-sort"></i></th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; cursor: pointer; width: 100px;">Kategori <i class="fa fa-sort"></i></th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Stok</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; width: 120px;">Harga Beli</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; width: 120px;">Harga Jual</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; width: 150px;">Tanggal Ditambahkan</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['id_cat']) && $_GET['id_cat']) {
                                $root->tampil_barang_filter($_GET['id_cat']);
                            }else{
                                $keyword=isset($_GET['q'])?$_GET['q']:"null";
                                $root->tampil_barang($keyword);
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
    
    .search-form {
        display: flex;
        align-items: center;
    }
</style>

<?php 
}
include "foot.php" ?>