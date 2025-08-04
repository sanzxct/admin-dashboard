<?php
session_start();

include 'koneksi.php';



$query = mysqli_query($conn, "SELECT * FROM pekerja");





//jumlah pekerja
$jumlah = mysqli_query($conn, "SELECT COUNT(*) as total FROM pekerja");
$jumlahPekerja = mysqli_fetch_assoc($jumlah)['total'];

//total gaji bulanan
$gaji = mysqli_query($conn, "SELECT SUM(gaji) as total_gaji FROM pekerja");
$totalGaji = mysqli_fetch_assoc($gaji)['total_gaji'];

$max = mysqli_query($conn, "SELECT MAX(gaji) as max_gaji FROM pekerja");
$gajiTertinggi = mysqli_fetch_assoc($max)['max_gaji'];



// Pencarian
if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
    $query = mysqli_query($conn, "SELECT * FROM pekerja WHERE nama LIKE '%$cari%'");
} else {
    $query = mysqli_query($conn, "SELECT * FROM pekerja");
}


//hapus data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = mysqli_query($conn, "DELETE FROM pekerja WHERE id = $id");

    if ($delete) {
        $_SESSION['success'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($conn);
    }
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="dashboard.css">
    <title>dashboard pekerja</title>
</head>

<body>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success" id="alert-show">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="left">
            <h1>StaffHub</h1>
            <div class="nav">
                <ul>
                    <li><a href="admin.php"><i class="fas fa-home"></i>Dashboard</a></li>
                    <li><a href="edit.php"><i class="fas fa-edit"></i>Edit Data</a></li>
                    <li><a href="admin.php"><i class="fas fa-plus"></i>Tambah Data</a></li>
                    <li><a href="admin.php"><i class="fas fa-trash-alt"></i>Hapus Data</a></li>
                    <li><a href="admin.php"><i class="fas fa-file-invoice-dollar"></i>Laporan Gaji</a></li>
                    <li><a href="admin.php"><i class="fas fa-user"></i>Profil Saya</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="right">
            <header class="header">
                Edit Data Pekerja
            </header>

            <div class="card">
                <div class="card-item">
                    <h3>Total Pekerja</h3>
                    <p><?= $jumlahPekerja ?> x</p>
                </div>
                <div class="card-item">
                    <h3>Total Gaji Bulanan</h3>
                    <p>Rp <?= number_format($totalGaji, 0, ',', '.') ?></p>
                </div>
                <div class="card-item">
                    <h3>Gaji Tertinggi</h3>
                    <p>Rp <?= number_format($gajiTertinggi, 0, ',', '.') ?></p>
                </div>
            </div>

            <form method="GET" class="search-form">
                <div class="search-container">
                    <input type="text" name="cari" placeholder="Cari nama..."
                        value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>" />
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>


            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Posisi</th>
                        <th>Gaji</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['posisi']) ?></td>
                            <td>Rp. <?= number_format($row['gaji'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <a href="#" class="btn-delete" onclick="event.preventDefault(); confirmDelete(<?= $row['id'] ?>);">
                                    <i class="fas fa-trash-alt"></i>
                                    Hapus
                                </a>

                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>



            <script>
                function confirmDelete(id) {
                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#DC2626',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'hapus.php?id=' + id;
                        }
                    });
                }
            </script>