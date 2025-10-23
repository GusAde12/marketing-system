<?php
session_start();
include "koneksi.php"; // atau config.php jika nama file koneksi berbeda

if (isset($_GET['id_barang']) && is_numeric($_GET['id_barang'])) {
    $id_barang = intval($_GET['id_barang']);

    try {
        // Ambil koneksi dari $conn
        $conn->begin_transaction();

        // 1. Ambil semua id_produksi yang berhubungan dengan barang ini
        $get_produksi = $conn->query("SELECT id_produksi FROM detail_produksi WHERE id_barang_jadi = $id_barang");
        $id_produksi_arr = [];
        while ($row = $get_produksi->fetch_assoc()) {
            $id_produksi_arr[] = $row['id_produksi'];
        }

        // 2. Hapus detail produksi terkait
        $conn->query("DELETE FROM detail_produksi WHERE id_barang_jadi = $id_barang");

        // 3. Hapus data produksi yang tidak punya detail lagi
        if (!empty($id_produksi_arr)) {
            foreach ($id_produksi_arr as $id_produksi) {
                $cek = $conn->query("SELECT COUNT(*) as total FROM detail_produksi WHERE id_produksi = $id_produksi");
                $row = $cek->fetch_assoc();
                if ($row['total'] == 0) {
                    $conn->query("DELETE FROM produksi WHERE id_produksi = $id_produksi");
                }
            }
        }

        // Opsional: hapus data barang juga jika memang dimaksudkan
        $conn->query("DELETE FROM barang WHERE id_barang = $id_barang");

        $conn->commit();
        $_SESSION['alert'] = "Data produksi berhasil dihapus.";
        $_SESSION['alert_type'] = "success";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['alert'] = "Gagal menghapus data produksi: " . $e->getMessage();
        $_SESSION['alert_type'] = "danger";
    }
} else {
    $_SESSION['alert'] = "ID barang tidak valid.";
    $_SESSION['alert_type'] = "warning";
}

header("Location: barang_produksi.php");
exit();
