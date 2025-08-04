<?php
include 'koneksi.php';
$query = mysqli_query($conn, "SELECT * FROM pekerja");


// Total gaji semua pekerja
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(gaji) AS total FROM pekerja"))['total'];

// Gaji per posisi
$perPosisi = mysqli_query($conn, "SELECT posisi, SUM(gaji) AS total FROM pekerja GROUP BY posisi");

// Gaji tertinggi & terendah
$max = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(gaji) AS max FROM pekerja"))['max'];
$min = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MIN(gaji) AS min FROM pekerja"))['min'];



$labels = [];
$jumlahPosisi = [];

$data = mysqli_query($conn, "SELECT posisi, COUNT(*) as jumlah FROM pekerja GROUP BY posisi");
while ($row = mysqli_fetch_assoc($data)) {
    $labels[] = $row['posisi'];
    $jumlahPosisi[] = $row['jumlah'];
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <link rel="stylesheet" href="laporan.css">
    <script>
        const posisiLabels = <?= json_encode($labels) ?>;
        const posisiJumlah = <?= json_encode($jumlahPosisi) ?>;
    </script>

    <title>dashboard pekerja</title>
</head>

<body>
    <div class="header">
        <h1>Laporan Gaji Pekerja</h1>
    </div>
    <div class="card">
        <div class="card-item">
            <h3>Total Gaji Pekerja</h3>
            <p>Rp <?= number_format($total, 0, ',', '.') ?></p>
        </div>
        <div class="card-item">
            <h3>Gaji Tertinggi</h3>
            <p>Rp <?= number_format($max, 0, ',', '.') ?></p>
        </div>
        <div class="card-item">
            <h3>Gaji Terendah</h3>
            <p>Rp <?= number_format($min, 0, ',', '.') ?></p>
        </div>
    </div>

    <section class="chart-section">
        <h3 style="text-align:center; font-size:20px;">Distribusi Posisi Pekerja</h3>
        <canvas id="pieChart" width="400" height="400"></canvas>
    </section>

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
    <<a href="export_excel.php" class="btn" target="_blank">
        <i class="fas fa-file-excel"></i> Export ke Excel



</body>

</html>


<script>
    const ctx = document.getElementById('pieChart').getContext('2d');
    const data = {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Jumlah Pekerja',
            data: <?= json_encode($jumlahPosisi) ?>,
            backgroundColor: [
                '#60A5FA', '#34D399', '#F87171', '#FBBF24', '#A78BFA',
                '#FB7185', '#FCD34D', '#4ADE80', '#818CF8', '#FCA5A5'
            ]
        }]
    };

    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);

    const config = {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    color: '#fff',
                    formatter: (value, context) => {
                        let percent = (value / total * 100).toFixed(1);
                        return percent + '%';
                    },
                    font: {
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        },
        plugins: [ChartDataLabels]
    };

    new Chart(ctx, config);
</script>