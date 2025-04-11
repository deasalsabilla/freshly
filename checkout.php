<?php
session_start();
include 'admin/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(["success" => false, "message" => "Silakan login terlebih dahulu!"]);
    exit;
}

$id_user = $_SESSION['id_user'];
$tgl_jual = date('Y-m-d H:i:s');

// Ambil data pesanan user
$query_pesanan = "SELECT p.*, pr.harga, pr.stok 
                  FROM tb_pesanan p 
                  JOIN tb_produk pr ON p.id_produk = pr.id_produk 
                  WHERE p.id_user = '$id_user'";
$result_pesanan = mysqli_query($koneksi, $query_pesanan);

if (mysqli_num_rows($result_pesanan) == 0) {
    echo json_encode(["success" => false, "message" => "Keranjang kosong!"]);
    exit;
}

$subtotal = 0;
$pesanan_data = [];

while ($row = mysqli_fetch_assoc($result_pesanan)) {
    $qty = $row['qty'];
    $harga = $row['harga'];
    $stok = $row['stok'];

    if ($qty > $stok) {
        echo json_encode(["success" => false, "message" => "Stok tidak cukup untuk produk: {$row['nm_produk']}"]);
        exit;
    }

    $total_item = $qty * $harga;
    $subtotal += $total_item;

    $pesanan_data[] = [
        'id_produk' => $row['id_produk'],
        'qty' => $qty,
        'harga' => $harga,
        'total_item' => $total_item
    ];
}

// Hitung diskon
$diskon = 0;
if ($subtotal > 150000 && $subtotal <= 500000) {
    $diskon = 0.05 * $subtotal;
} elseif ($subtotal > 500000) {
    $diskon = 0.08 * $subtotal;
}
$total_bayar = $subtotal - $diskon;

// Buat id_jual baru
$query_id = "SELECT MAX(id_jual) AS last_id FROM tb_jual";
$result_id = mysqli_query($koneksi, $query_id);
$row_id = mysqli_fetch_assoc($result_id);
$last_id = $row_id['last_id'];

if ($last_id) {
    $new_id = 'T' . str_pad((intval(substr($last_id, 1)) + 1), 3, '0', STR_PAD_LEFT);
} else {
    $new_id = 'T001';
}

// Simpan ke tb_jual
$query_jual = "INSERT INTO tb_jual (id_jual, id_user, tgl_jual, total, diskon)
               VALUES ('$new_id', '$id_user', '$tgl_jual', '$total_bayar', '$diskon')";
if (!mysqli_query($koneksi, $query_jual)) {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan ke tb_jual: " . mysqli_error($koneksi)]);
    exit;
}

// Simpan ke tb_jualdtl dan kurangi stok
foreach ($pesanan_data as $item) {
    $id_produk = $item['id_produk'];
    $qty = $item['qty'];
    $harga = $item['harga'];

    // Insert detail
    $query_dtl = "INSERT INTO tb_jualdtl (id_jual, id_produk, qty, harga)
                  VALUES ('$new_id', '$id_produk', '$qty', '$harga')";
    mysqli_query($koneksi, $query_dtl);

    // Kurangi stok
    $query_stok = "UPDATE tb_produk SET stok = stok - $qty WHERE id_produk = '$id_produk'";
    mysqli_query($koneksi, $query_stok);
}

// Hapus isi keranjang
mysqli_query($koneksi, "DELETE FROM tb_pesanan WHERE id_user = '$id_user'");

echo json_encode(["success" => true, "message" => "Checkout berhasil!", "id_jual" => $new_id]);
?>