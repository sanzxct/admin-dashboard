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


// Data untuk grafik
$data = mysqli_query($conn, "SELECT posisi, COUNT(*) as jumlah FROM pekerja GROUP BY posisi ");
$labels = [];
$jumlahPosisi = [];

while ($row = mysqli_fetch_assoc($data)) {
    $labels[] = $row['posisi'];
    $jumlahPosisi[] = $row['jumlah'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['id'];
  $nama = $_POST['nama'];
  $posisi = $_POST['posisi'];
  $gaji = $_POST['gaji'];
  $status = $_POST['status'];

  $update = mysqli_query($conn, "UPDATE pekerja SET nama='$nama', posisi='$posisi', gaji='$gaji', status='$status' WHERE id=$id");


  if ($update) {
      $_SESSION['success'] = "Data berhasil diupdate!";
  } else {
      $_SESSION['error'] = "Gagal mengupdate data: " . mysqli_error($conn);
  } 
  header("Location: edit.php");
    exit;
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

    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                <div class="none">
                    <a href="#" class="btn-edit"
                     onclick="openEditForm(
                        <?= $row['id'] ?>,
                     '<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($row['posisi'], ENT_QUOTES) ?>',
                    <?= $row['gaji'] ?>,
                    '<?= $row['status'] ?>'
                    )">Edit</a>

                </div>

            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

            <div id="editForm" class="modal" style="display:none;">
                <div class="modal-content">
                    <h3 style="margin-bottom: 20px; text-align:center">Edit Data</h3>
                    <form action="edit.php" method="POST">
                        <input type="hidden" name="id" id="edit-id">
                        <label>Nama</label>
                        <input type="text" name="nama" id="edit-nama" required>
                        <label>Posisi</label>
                        <input type="text" name="posisi" id="edit-posisi" required>
                        <label>Gaji</label> 
                        <input type="number" name="gaji" id="edit-gaji" required>
                        <label>Status</label>   
                        <select name="status" id="edit-status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                        <button type="submit">Simpan</button>
                        <button type="button" onclick="document.getElementById('editForm').style.display='none'">Batal</button>
                    </form>

                </div>
            </div>

        </div>
    </div>


<script>
    function openEditForm(id, nama, posisi,gaji, status){
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-posisi').value = posisi;
        document.getElementById('edit-gaji').value = gaji;
        document.getElementById('edit-status').value = status;

        document.getElementById('editForm').style.display = 'block';
        
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
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