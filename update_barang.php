<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventaris_barang";

$conn = new mysqli($servername, $username, $password, $dbname);

// Ambil ID dari URL
$id = $_GET['id'];

// Ambil data barang
$sql = "SELECT * FROM barang WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Proses update jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama_barang'];
    $kategori = $_POST['kategori_barang'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga_barang'];

    $update = "UPDATE barang SET 
        nama_barang = '$nama', 
        kategori_barang = '$kategori', 
        stok = '$stok', 
        harga_barang = '$harga' 
        WHERE id = $id";
    
    if ($conn->query($update)) {
        header("Location: barang_success.php");
        exit;
    } else {
        echo "Gagal mengupdate data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Data Barang</h1>
    <form method="POST">
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" value="<?= $row['nama_barang'] ?>" required>
        </div>
        <div class="form-group">
            <label>Kategori</label>
            <input type="text" name="kategori_barang" value="<?= $row['kategori_barang'] ?>" required>
        </div>
        <div class="form-group">
            <label>Stok</label>
            <input type="text" name="stok" value="<?= $row['stok'] ?>" required>
        </div>
        <div class="form-group">
            <label>Harga Barang</label>
            <input type="text" name="harga_barang" value="<?= $row['harga_barang'] ?>" required>
        </div>
        <button type="submit" class="submit-btn">Update</button>
    </form>
</div>
</body>
</html>
