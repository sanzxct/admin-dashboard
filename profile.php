<?php
session_start();
include 'koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    echo "Anda belum login!";
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pekerja berdasarkan user_id
$query = mysqli_query($conn, "SELECT * FROM pekerja WHERE user_id = $user_id");
$data = mysqli_fetch_assoc($query);

// Proses upload foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $fotoName = $_FILES['foto']['name'];
    $fotoTmp = $_FILES['foto']['tmp_name'];
    $folder = "uploads/";

    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    $targetFile = $folder . basename($fotoName);

    if (move_uploaded_file($fotoTmp, $targetFile)) {
        $update = mysqli_query($conn, "UPDATE pekerja SET foto = '$fotoName' WHERE user_id = $user_id");

        if ($update) {
            echo "<script>alert('Foto berhasil diunggah!'); window.location='profile.php';</script>";
            exit;
        } else {
            echo "Gagal menyimpan nama file ke database.";
        }
    } else {
        echo "Gagal mengupload file.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pekerja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css"> <!-- jika css terpisah -->
</head>
<body>
<div class="profile-full">

    <!-- Foto Profil -->
    <div class="profile-photo">
        <?php if (!empty($data['foto'])): ?>
            <img src="uploads/<?= htmlspecialchars($data['foto']) ?>" alt="Foto Profil">
        <?php else: ?>
            <img src="default.png" alt="Default Foto">
        <?php endif; ?>
    </div>

    <!-- Detail Profil -->
    <div class="profile-detail">
        <h1><?= htmlspecialchars($data['nama']) ?></h1>
        <div class="role"><?= htmlspecialchars($data['posisi']) ?></div>
        <p><strong>Gaji:</strong> Rp<?= number_format($data['gaji'], 0, ',', '.') ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($data['status']) ?></p>
    </div>

    <!-- Form Upload -->
    <div class="profile-actions">
        <form method="POST" enctype="multipart/form-data">
            <label for="foto">Upload Foto Baru:</label><br>
            <input type="file" name="foto" id="foto" required><br>
            <button type="submit">Upload</button>
        </form>

        <a href="admin.php" class="btn secondary">Kembali ke Dashboard</a>
    </div>

</div>
</body>
</html>
