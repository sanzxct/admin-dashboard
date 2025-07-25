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
    <title>dashboard pekerja</title>
</head>
<body>
    <div class="container">
        <div class="left">
              <h1>StaffHub</h1>
            <div class="nav">
                <ul>
                <li><a href="admin.php"><i class="fas fa-home"></i>Dashboard</a></li>
                <li><a href="edit.php"><i class="fas fa-edit"></i>Edit Data</a></li>
                <li><a href="tambah.php"><i class="fas fa-plus"></i>Tambah Data</a></li>
                <li><a href="admin.php"><i class="fas fa-trash-alt"></i>Hapus Data</a></li>
                <li><a href="admin.php"><i class="fas fa-file-invoice-dollar"></i>Laporan Gaji</a></li>
                <li><a href="admin.php"><i class="fas fa-user"></i>Profil Saya</a></li>
                  <li><a href="login.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                </ul>
            </div>
         </div>
            <div class="right">
                <header class="header">
                    Dashboard Pekerja
                </header>
                
                <div class="greet">
                <h2>Hai, <?= $_SESSION['username'] ?> </h2>
                <p>Selamat datang di panel manajemen data pekerja.</p>
            </div>

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
                        <th>Nama Pekerja</th>
                        <th>Posisi</th>
                        <th>Gaji Bulanan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                     <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                
                    <td>
                        <?= htmlspecialchars($row['nama']) ?>
                    </td>
                    <td>
                       <?= htmlspecialchars($row['posisi']) ?>
                    </td>
                    <td>
                       RP.<?= number_format($row['gaji'], 0, ',', '.') ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['status']) ?>
                    </td>

                    </tr>

                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada data pekerja ditemukan.</td>
                        <?php endif; ?>
                </tbody>
            </table>

            <div class="chart-card">
                 <h3><i class="fas fa-chart-bar"></i> Jumlah Pekerja per Posisi</h3>
                    <div class="chart-wrapper">
                         <canvas id="grafikPekerja"></canvas>
                    </div>
            </div>



            </div>
    </div>


<script>
  const ctx = document.getElementById('grafikPekerja').getContext('2d');
  const grafikPekerja = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [{
        label: 'Jumlah Pekerja',
        data: <?= json_encode($jumlahPosisi) ?>,
        backgroundColor: [
          '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6' // warna bar
        ],
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        duration: 1000,
        easing: 'easeOutQuart'
      },
      plugins: {
        tooltip: {
          backgroundColor: '#1E293B',
          titleColor: '#FBBF24',
          bodyColor: '#ffffff',
          borderWidth: 1,
          borderColor: '#FBBF24',
          padding: 10
        },
        legend: {
          display: false
        },
        title: {
          display: false
        }
      },
      scales: {
        x: {
          ticks: {
            color: '#374151', // abu tua
            font: { weight: '500' }
          },
          grid: {
            display: false
          }
        },
        y: {
          beginAtZero: true,
          ticks: {
            color: '#374151',
            font: { weight: '500' },
            stepSize: 1
          },
          grid: {
            color: '#E5E7EB' // grid garis halus
          }
        }
      }
    }
  });
</script>

</body>
</html>