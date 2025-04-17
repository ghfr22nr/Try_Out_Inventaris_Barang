<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventaris_barang";

$conn = new mysqli($servername, $username, $password, $dbname);

$id = $_GET['id'];

$sql = "DELETE FROM barang WHERE id = $id";

if ($conn->query($sql)) {
    header("Location: barang_success.php");
    exit;
} else {
    echo "Gagal menghapus data.";
}
?>
