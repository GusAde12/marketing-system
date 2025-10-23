<?php include "head.php" ?>
<?php
    if (isset($_GET['action']) && $_GET['action']=="edit_kategori") {
        include "edit_kategori.php";
    }
    else{
?>
<script type="text/javascript">
    document.title="Kategori Barang";
    document.getElementById('kategori').classList.add('active');
</script>
<div class="content">
    <div class="padding">
        <div class="bgwhite" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="padding">
                <div class="contenttop">
                    <div class="left">
                        <form action="handler.php?action=tambah_kategori" method="post" style="display: flex; align-items: center; gap: 10px;">
                            <input type="text" name="nama_kategori" placeholder="Nama Kategori..." style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; flex-grow: 1; max-width: 300px;">
                            <button type="submit" class="btnblue" style="padding: 8px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center;">
                                <i class="fa fa-plus" style="margin-right: 8px;"></i> Tambahkan
                            </button>
                        </form>
                    </div>
                    <div class="both"></div>
                </div>
                
                <div class="datainfo" style="margin: 20px 0;">
                    <span class="label" style="font-size: 14px; color: #555;">Jumlah Kategori: <strong><?= $root->show_jumlah_cat() ?></strong></span>
                </div>
                
                <div class="table-responsive">
                    <table class="datatable" style="width: 100%; border-collapse: collapse; max-width: 1200px;">
                        <thead>
                            <tr style="background-color: #f8f9fa; color: #495057;">
                                <th width="35px" style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">NO</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Nama Kategori</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $root->tampil_kategori() ?>
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
    
    .table-responsive {
        overflow-x: auto;
    }
</style>

<?php 
}
include "foot.php" ?>