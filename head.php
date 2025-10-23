<?php 
include "root.php"; 
session_start();
if (!isset($_SESSION['username'])) {
    $root->redirect("index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/index.css">
    <link rel="stylesheet" type="text/css" href="assets/awesome/css/font-awesome.min.css">
    <script type="text/javascript" src="assets/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    /* Sidebar Modern Professional Style */
    .sidebar {
        width: 260px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background: #1e1e2f;
        color: white;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        z-index: 1000;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        flex-shrink: 0;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .sidebar h3 {
        padding: 20px;
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        text-align: center;
        background-color: #181828;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        letter-spacing: 1px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    /* Custom Scrollbar */
    .sidebar-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar-content::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
    }
    
    .sidebar-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }
    
    .sidebar-content::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .admin-info {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        background-color: #181828;
    }

    .admin-info img {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 60px;
        border: 2px solid #3498db;
    }

    .admin-info span {
        font-weight: 500;
        font-size: 15px;
        color: #ecf0f1;
    }

    .sidebar li a {
        display: flex;
        align-items: center;
        padding: 14px 20px;
        color: #bdc3c7;
        text-decoration: none;
        transition: background 0.3s, color 0.3s;
        font-size: 15px;
        border-left: 3px solid transparent;
    }

    .sidebar li a i {
        width: 24px;
        text-align: center;
        margin-right: 12px;
        font-size: 16px;
    }

    .sidebar li a:hover,
    .sidebar li a.active {
        background-color: #2a2a40;
        border-left: 3px solid #3498db;
        color: #ffffff;
    }

    .sidebar li a.active i {
        color: #3498db;
    }

    /* Main content adjustment */
    .nav, .content {
        margin-left: 260px;
        transition: margin 0.3s ease;
    }

    /* Navbar adjustment */
    .nav {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        position: relative;
        z-index: 900;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        transform: translateX(-2px); /* Geser ke kiri */
        width: calc(100% + 30px);     /* Lebarkan biar gak kepotong */
    }
    .menu-abu-abu a {
        color: #888888 !important; /* abu-abu */
    }

    .menu-abu-abu a:hover {
        color:rgb(68, 67, 67) !important; /* abu-abu lebih gelap saat hover */
    }

    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fa fa-shopping-cart"></i> SISTEM PENJUALAN</h3>
    </div>
    <div class="sidebar-content">
        <ul><?php
                if ($_SESSION['status']==1) {
                    ?>
                        <li class="admin-info">
                            <img src="assets/img/kayu.png">
                            <span><?php echo $_SESSION['username']; ?></span>
                        </li>
                        <li><a id="dash" href="home.php"><i class="fa fa-home"></i> Dashboard</a></li>
                        <li><a id="barang" href="barang.php"><i class="fa fa-cube"></i> Barang</a></li>
                        <li><a id="barang_produksi" href="barang_produksi.php"><i class="fa fa-cube"></i> Barang Produksi</a></li>
                        <li><a id="distributor" href="distributor.php"><i class="fa fa-truck"></i> Distributor</a></li>
                        <li><a id="users" href="users.php"><i class="fa fa-user"></i> User</a></li>
                        <li><a id="kategori" href="kategori.php"><i class="fa fa-tags"></i> Kategori Barang</a></li>
                        <li><a id="pembelian" href="pembelian.php"><i class="fa fa-shopping-cart"></i> Pembelian</a></li>
                        <li><a id="transaksi" href="transaksi.php"><i class="fa fa-credit-card"></i> Transaksi</a></li>
                        <li><a id="stok" href="stok.php"><i class="fa fa-cubes"></i> Stok Opname</a></li>
                        <li><a id="laporan" href="laporan.php"><i class="fa fa-file-text"></i> Laporan</a></li>
                    <?php
                }else{
                    ?>
                        <li><a id="dash" href="home.php"><i class="fa fa-home"></i> Dashboard</a></li>
                        <li><a id="laporan" href="laporan.php"><i class="fa fa-file-text"></i> Laporan</a></li>
                    <?php
                }
            ?>
            </ul>
    </div>
</div>
<div class="nav">
    <ul class="menu-abu-abu">
        <li><a href=""><i class="fa fa-user"></i> <?= $_SESSION['username'] ?></a>
        <ul>
            <?php
            if ($_SESSION['status']==1) {
                ?>
            <li><a href="setting_akun.php"><i class="fa fa-cog"></i> Pengaturan Akun</a></li>
            <?php } ?>
            <li><a href="handler.php?action=logout"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
        </li>
    </ul>
</div>