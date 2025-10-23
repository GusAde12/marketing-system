<?php include "head.php" ?>
<?php
    if (isset($_GET['action']) && $_GET['action']=="tambah_distributor") {
        include "tambah_distributor.php";
    }
    else if (isset($_GET['action']) && $_GET['action']=="edit_distributor") {
        include "edit_distributor.php";
    }
    else{
?>
<script type="text/javascript">
    document.title="Distributor";
    document.getElementById('distributor').classList.add('active');
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
                6:{sorter:false}
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
                        <a href="?action=tambah_distributor" class="btnblue" style="padding: 10px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center; margin-right: 10px;">
                            <i class="fa fa-plus" style="margin-right: 8px;"></i> Tambah Distributor
                        </a>
                        <a href="cetak_barang.php" class="btnblue" target="_blank" style="padding: 10px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center;">
                            <i class="fa fa-print" style="margin-right: 8px;"></i> Cetak
                        </a>
                    </div>
                    <div class="right">
                        <form class="search-form">
                            <input type="search" name="q" placeholder="Cari Distributor..." value="<?php echo $keyword=isset($_GET['q'])?$_GET['q']:""; ?>" style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; width: 250px;">
                            <button type="submit" style="background: none; border: none; margin-left: -30px; color: #6c757d;">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="both"></div>
                </div>
                
                <div class="datainfo" style="margin: 20px 0;">
                    <span class="label" style="font-size: 14px; color: #555;">Jumlah Distributor: <strong><?= $root->show_jumlah_distributor() ?></strong></span>
                </div>
                
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="datatable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; color: #495057;">
                                <th width="35px" style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">#</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; cursor: pointer; width: 100px;">Nama Pemasok <i class="fa fa-sort"></i></th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; cursor: pointer;">Alamat <i class="fa fa-sort"></i></th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Telp</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; width: 120px;">Penanggung Jawab</th>
                                <!-- <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6; width: 120px;">Status</th> -->
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['id_distributor']) && $_GET['id_distributor']) {
                                $root->tampil_distributor_filter($_GET['id_distributor']);
                            }else{
                                $keyword=isset($_GET['q'])?$_GET['q']:"null";
                                $root->tampil_distributor($keyword);
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
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-active {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
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
    
    .search-form {
        display: flex;
        align-items: center;
    }
</style>

<?php 
}
include "foot.php" ?>