<?php
session_start();   
include 'koneksi.php';

// Tambah data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_tambah'])) {
  $nama = $_POST['nama'];
  $posisi = $_POST['posisi'];
  $gaji = $_POST['gaji'];
  $status = $_POST['status'];

  $add = mysqli_query($conn, "INSERT INTO pekerja (nama, posisi, gaji, status) VALUES ('$nama', '$posisi', '$gaji', '$status')");
  if ($add) {
      $_SESSION['success'] = "Data berhasil ditambahkan!";
  } else {
      $_SESSION['error'] = "Gagal menambahkan data: " . mysqli_error($conn);
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

$query = mysqli_query($conn, "SELECT * FROM pekerja");
$jumlah = mysqli_query($conn, "SELECT COUNT(*) as total FROM pekerja");
$jumlahPekerja = mysqli_fetch_assoc($jumlah)['total'];

$gaji = mysqli_query($conn, "SELECT SUM(gaji) as total_gaji FROM pekerja");
$totalGaji = mysqli_fetch_assoc($gaji)['total_gaji'];

$max = mysqli_query($conn, "SELECT MAX(gaji) as max_gaji FROM pekerja");
$gajiTertinggi = mysqli_fetch_assoc($max)['max_gaji'];

if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
    $query = mysqli_query($conn, "SELECT * FROM pekerja WHERE nama LIKE '%$cari%'");
} else {
    $query = mysqli_query($conn, "SELECT * FROM pekerja");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="dashboard.css"/>
  <title>Dashboard Pekerja</title>
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
    <header class="header">Edit Data Pekerja</header>

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
        <input type="text" name="cari" placeholder="Cari nama..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>" />
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
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['posisi']) ?></td>
            <td>Rp. <?= number_format($row['gaji'], 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="button-container" style="text-align: center; margin-top: 20px;">
      <a href="#" class="button-edit" onclick="document.getElementById('tambahForm').style.display='block'">Tambah Data</a>
    </div>

    <div id="tambahForm" class="modal" style="display:none;">
      <div class="modal-content">
        <h3 style="margin-bottom: 20px; text-align:center">Tambah Data</h3>
        <form action="" method="POST">
          <input type="hidden" name="submit_tambah" value="1">
          <label>Nama</label>
          <input type="text" name="nama" required>
          <label>Posisi</label>
          <input type="text" name="posisi" required>
          <label>Gaji</label>
          <input type="number" name="gaji" required>
          <label>Status</label>
          <select name="status" required>
            <option value="Aktif">Aktif</option>
            <option value="Tidak Aktif">Tidak Aktif</option>
          </select>
          <button type="submit">Simpan</button>
          <button type="button" onclick="document.getElementById('tambahForm').style.display='none'">Batal</button>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>

<script>
function openTambahForm(id, nama, posisi, gaji, status) {
    document.getElementById('tambah-id').value = id;
    document.getElementById('tambah-nama').value = nama;
    document.getElementById('tambah-posisi').value = posisi;
    document.getElementById('tambah-gaji').value = gaji;
    document.getElementById('tambah-status').value = status;

    document.getElementById('tambahForm').style.display = 'block';
}

    function closeTambahForm() {
        document.getElementById('tambahForm').style.display = 'none';
    }

    
    document.addEventListener("DOMContentLoaded", function() {
        const alert = document.getElementById('alert-show');
        if(alert && alert.textContent.trim() !=""){
            alert.classList.add('show');

            setTimeout(() => {
                alert.classList.remove('show');
                
            setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 3000); 
        }
    })
</script>
