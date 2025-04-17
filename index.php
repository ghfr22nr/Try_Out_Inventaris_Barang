<?php
$harga_barang = isset($_POST['harga_barang']) ? $_POST['harga_barang'] : '0';

// Bersihkan format (hapus Rp, titik, koma, dll)
$harga_barang = preg_replace('/[^\d]/', '', $harga_barang);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inventaris Barang</title>
  <link rel="stylesheet" href="style.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
    rel="stylesheet" />
</head>

<body>
  <!-- Navbar -->
  <div class="navbar">
    <a href="index.php">Form Barang Masuk</a>
    <a href="barang_success.php">Tabel Barang Masuk</a>
  </div>

  <div class="container">
    <h1>Form Barang Masuk</h1>
    <form action="submit_barang.php" method="POST">
      <div class="form-group">
        <label for="nama_barang">Nama Barang:</label>
        <input type="text" id="nama_barang" name="nama_barang" required />
      </div>

      <div class="form-group">
        <label for="kategori_barang">Kategori Barang:</label>
        <input type="text" id="kategori_barang" name="kategori_barang" required />
      </div>

      <div class="form-group">
        <label for="stok">Jumlah Stok:</label>
        <input type="text" id="stok" name="stok" required />
      </div>

      <div class="form-group">
        <label for="harga_barang">Harga Barang:</label>
        <input type="text" id="harga_barang" name="harga_barang" required />
      </div>

      <div class="form-group">
        <button type="submit" class="submit-btn">Submit</button>
      </div>
    </form>
  </div>


  <script>
    function formatRupiah(input) {
      let value = input.value.replace(/[^\d]/g, '');
      if (!value) return input.value = '';

      input.value = 'Rp' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
  </script>

</body>

</html>