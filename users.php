<?php include "head.php" ?>
<?php
    if (isset($_GET['action']) && $_GET['action']=="tambah_user") {
        include "tambah_user.php";
    }
    else if (isset($_GET['action']) && $_GET['action']=="edit_user") {
        include "edit_user.php";
    }
    else {
?>
<script type="text/javascript">
    document.title="Data User";
    document.getElementById('users').classList.add('active');
</script>

<div class="content">
    <div class="padding">
        <div class="bgwhite" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="padding">
                <div class="contenttop">
                    <div class="left">
                        <a href="?action=tambah_user" class="btnblue" style="padding: 10px 20px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center;">
                            <i class="fa fa-plus" style="margin-right: 8px;"></i> Tambah User
                        </a>
                    </div>
                    <div class="both"></div>
                </div>
                
                <div class="datainfo" style="margin: 20px 0;">
                    <span class="label" style="font-size: 14px; color: #555;">Jumlah User: <strong><?= $root->show_jumlah_kasir() ?></strong></span>
                </div>
                
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="datatable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; color: #495057;">
                                <th width="35px" style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6;">#</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Nama</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Username</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Alamat</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Telp</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6;">Status</th>
                                <th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Tanggal Didaftarkan</th>
                                <th style="padding: 12px 15px; text-align: center; border-bottom: 2px solid #dee2e6; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $root->tampil_user(); ?>
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
    
    .text-date {
        color: #6c757d;
        font-size: 0.9em;
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