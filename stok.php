<?php include "head.php" ?>
<?php
    if (isset($_GET['action']) && $_GET['action']=="tambah_stok") {
        include "tambah_stok.php";
    }
    else if (isset($_GET['action']) && $_GET['action']=="edit_stok") {
        include "edit_stok.php";
    }
    else {
?>
<script type="text/javascript">
    document.title="Stok Opname";
    document.getElementById('stok').classList.add('active');
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
                7:{sorter:false}
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
                        <a href="?action=tambah_stok" class="btnblue" style="padding: 10px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center; margin-right: 10px;">
                            <i class="fa fa-plus" style="margin-right: 8px;"></i> Tambah Stok
                        </a>
                        <a href="cetak_barang.php" class="btnblue" target="_blank" style="padding: 10px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center;">
                            <i class="fa fa-print" style="margin-right: 8px;"></i> Cetak
                        </a>
                    </div>
                    <div class="right">
                        <form class="search-form">
                            <input type="search" name="q" placeholder="Cari Data Stok..." value="<?php echo $keyword=isset($_GET['q'])?$_GET['q']:""; ?>" style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; width: 250px;">
                            <button type="submit" style="background: none; border: none; margin-left: -30px; color: #6c757d;">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="both"></div>
                </div>
                
                <div class="datainfo" style="margin: 20px 0;">
                    <span class="label" style="font-size: 14px; color: #555;">Stok Opname</span>
                </div>
                
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="datatable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; color: #495057;">
                                <th width="35px" style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6;">#</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Nama Barang</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Tanggal</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6;">Stok Barang</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6;">Jumlah Asli</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6;">Selisih</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Keterangan</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $keyword = isset($_GET['q']) ? $_GET['q'] : "null";
                            $root->tampil_stok($keyword);
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
    
    .selisih-positive {
        color: #28a745;
        font-weight: bold;
    }
    
    .selisih-negative {
        color: #dc3545;
        font-weight: bold;
    }
    
    .selisih-neutral {
        color: #6c757d;
        font-weight: bold;
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
    
    .empty-state {
        padding: 20px;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        margin-bottom: 10px;
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