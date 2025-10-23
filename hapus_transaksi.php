<?php
session_start();
require_once "config.php"; // Include your database configuration

// Check if ID parameter exists and is valid
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_transaksi = (int)$_GET['id'];
    
    try {
        // Begin transaction
        $koneksi->begin_transaction();
        
        // 1. First delete related records (if any)
        $koneksi->query("DELETE FROM pembayaran WHERE id_transaksi = $id_transaksi");
        
        // 2. Then delete the main transaction
        $koneksi->query("DELETE FROM transaksi WHERE id_transaksi = $id_transaksi");
        
        // Commit transaction
        $koneksi->commit();
        
        $_SESSION['alert'] = 'Transaksi berhasil dihapus';
        $_SESSION['alert_type'] = 'success';
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['alert'] = 'Gagal menghapus transaksi: ' . $e->getMessage();
        $_SESSION['alert_type'] = 'error';
    }
} else {
    $_SESSION['alert'] = 'ID transaksi tidak valid';
    $_SESSION['alert_type'] = 'error';
}

// Redirect back to the report page
header("Location: laporan.php");
exit();
?>