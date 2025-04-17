<?php
// Database configuration
require_once 'config.php';

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input
$errors = [];
$nama_barang = trim($_POST['nama_barang'] ?? '');
$kategori_barang = trim($_POST['kategori_barang'] ?? '');
$stok = trim($_POST['stok'] ?? '');
$harga_barang = trim($_POST['harga_barang'] ?? '');

// Validation
if (empty($nama_barang)) {
    $errors[] = "Nama barang harus diisi";
}

if (empty($kategori_barang)) {
    $errors[] = "Kategori barang harus diisi";
}

if (!is_numeric($stok) || $stok < 0) {
    $errors[] = "Stok harus berupa angka positif";
}

if (!is_numeric($harga_barang) || $harga_barang < 0) {
    $errors[] = "Harga harus berupa angka positif";
}

if (empty($errors)) {
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO barang (nama_barang, kategori_barang, stok, harga_barang) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdd", $nama_barang, $kategori_barang, $stok, $harga_barang);
    
    if ($stmt->execute()) {
        header("Location: barang_success.php");
        exit();
    } else {
        $errors[] = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// If there are errors, handle them (you might want to redirect back with errors)
foreach ($errors as $error) {
    echo htmlspecialchars($error) . "<br>";
}

$conn->close();