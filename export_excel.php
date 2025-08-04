<?php
include 'koneksi.php';

// Jangan ada output apa pun sebelum header!
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_pekerja.xls");

$query = mysqli_query($conn, "SELECT * FROM pekerja");

if (!$query) {
    die("Gagal mengambil data: " . mysqli_error($conn));
}

echo "<table border='1'>";
echo "<tr>
        <th>Nama</th>
        <th>Posisi</th>
        <th>Gaji</th>
        <th>Status</th>
      </tr>";

while ($row = mysqli_fetch_assoc($query)) {
    echo "<tr>
            <td>{$row['nama']}</td>
            <td>{$row['posisi']}</td>
            <td>{$row['gaji']}</td>
            <td>{$row['status']}</td>
          </tr>";
}
echo "</table>";
?>
                