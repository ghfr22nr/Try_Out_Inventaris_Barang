<?php
// Database configuration
require_once 'config.php';

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

// Pagination settings
$rows_per_page = 10; // Number of rows to show per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $rows_per_page;

// Prepare base query for data
$sql = "SELECT * FROM barang WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM barang WHERE 1=1";
$params = [];
$types = '';

// Add search condition
if (!empty($search)) {
    $sql .= " AND nama_barang LIKE ?";
    $count_sql .= " AND nama_barang LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= 's';
}

// Add category filter
if (!empty($filter_kategori)) {
    $sql .= " AND kategori_barang = ?";
    $count_sql .= " AND kategori_barang = ?";
    $params[] = $filter_kategori;
    $types .= 's';
}

// Add sorting and pagination to main query
$sql .= " ORDER BY tanggal_masuk DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $rows_per_page;
$types .= 'ii';

// Get total count of records (for pagination)
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    // We need to remove the last two pagination parameters for the count query
    $count_params = array_slice($params, 0, count($params) - 2);
    $count_types = substr($types, 0, -2);
    if (!empty($count_types)) {
        $stmt_count->bind_param($count_types, ...$count_params);
    }
}
$stmt_count->execute();
$total_result = $stmt_count->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $rows_per_page);

// Get the actual data
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get categories for filter dropdown
$kategori_result = $conn->query("SELECT DISTINCT kategori_barang FROM barang");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Barang Yang Masuk</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="index.php">Form Barang Masuk</a>
        <a href="barang_success.php">Tabel Barang Masuk</a>
    </div>

    <div class="container2">
        <h1>Daftar Barang Yang Masuk</h1>

        <!-- Form Pencarian & Filter -->
        <form method="GET" action="barang_success.php" class="search-form">
            <input type="text" name="search" placeholder="Cari nama barang..." style="margin-bottom: 25px;"
                value="<?= htmlspecialchars($search) ?>">
            <select name="kategori" style="margin-bottom: 25px;">
                <option value="">Semua Kategori</option>
                <?php while ($row = $kategori_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['kategori_barang']) ?>"
                        <?= ($row['kategori_barang'] == $filter_kategori) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['kategori_barang']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Barang</th>
                            <th>Kategori Barang</th>
                            <th>Jumlah Stok</th>
                            <th>Harga Barang</th>
                            <th>Tanggal Masuk</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['kategori_barang']) ?></td>
                                <td><?= htmlspecialchars($row['stok']) ?></td>
                                <td>
                                    <?php
                                    $cleaned_harga = preg_replace('/[^\d]/', '', $row['harga_barang']);
                                    echo 'Rp' . number_format((float)$cleaned_harga, 0, ',', '.');
                                    ?>
                                </td>

                                <td><?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?></td>
                                <td class="action-links">
                                    <a href="update_barang.php?id=<?= htmlspecialchars($row['id']) ?>" class="action-link">Edit</a>
                                    <a href="delete_barang.php?id=<?= htmlspecialchars($row['id']) ?>"
                                        class="action-link delete"
                                        onclick="return confirm('Yakin ingin menghapus barang ini?');">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <h2>Daftar ID & Kategori Barang</h2>

            <?php
            // Ambil data ID dan kategori_barang dari tabel barang
            $id_kategori_result = $conn->query("SELECT id, kategori_barang FROM barang ORDER BY id ASC");
            ?>

            <?php if ($id_kategori_result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kategori Barang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $id_kategori_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['kategori_barang']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">Belum ada data ID dan Kategori yang tersedia.</p>
            <?php endif; ?>


            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="page-link">&laquo; First</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page - 1])) ?>" class="page-link">&lsaquo; Prev</a>
                <?php endif; ?>

                <?php
                // Show page numbers
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);

                for ($i = $start_page; $i <= $end_page; $i++):
                    if ($i == $current_page): ?>
                        <span class="page-link current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="page-link"><?= $i ?></a>
                <?php endif;
                endfor;
                ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page + 1])) ?>" class="page-link">Next &rsaquo;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="page-link">Last &raquo;</a>
                <?php endif; ?>
            </div>

            <div class="print-button-container">
                <button onclick="window.print();" class="btn btn-primary print-btn">Cetak PDF</button>
            </div>

        <?php else: ?>
            <p class="no-data">Belum ada barang yang ditemukan.</p>
        <?php endif; ?>
    </div>

    <?php
    $stmt->close();
    $stmt_count->close();
    $conn->close();
    ?>
</body>

</html>