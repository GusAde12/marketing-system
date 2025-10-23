<?php

include 'root.php';


//inisialisasi keranjang belanja jika belum ada

session_start(); // Memulai sesi

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

//mendapatkan daftar produk dari database
$sql ="SELECT*FROM transaksi";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaksi</title>
</head>
<body>
    <h1>Keranjang Penjualan</h1>
    <table border="1">
        <tr>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['price']; ?></td>
            <td>
                <form action="add_to_cart.php" method="post">
                   <input type="hidden" name="produk_id" value="<?php echo htmlspecialchars($row['produk_id']); ?>">
                    <button type="submit">Tambah ke Keranjang</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h1>Keranjang Belanja</h1>
    <table border="1">
        <tr>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>Aksi</th>
        </tr>
        <?php
        $total = 0;
        foreach ($_SESSION['cart'] as $produk_id => $quantity):
            $sql = "SELECT * FROM produk WHERE produk_id = $produk_id";
            $result = $conn->query($sql);
            $produk = $result->fetch_assoc();
            $subtotal = $produk['price'] * $quantity;
            $total += $subtotal;
        ?>
        <tr>
            <td><?php echo $produk['name']; ?></td>
            <td><?php echo $produk['price']; ?></td>
            <td><?php echo $quantity; ?></td>
            <td><?php echo $subtotal; ?></td>
            <td>
                <form action="remove_from_cart.php" method="post">
                    <input type="hidden" name="produk_id" value="<?php echo $produk_id; ?>">
                    <button type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3">Total</td>
            <td><?php echo $total; ?></td>
            <td>
                <form action="checkout.php" method="post">
                    <button type="submit">Checkout</button>
                </form>
            </td>
        </tr>
    </table>
</body>
</html>