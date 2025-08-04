<?php

include 'koneksi.php';
session_start();

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['LOGIN'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' ");
    $user = mysqli_fetch_assoc($query);

    if ($user && $user['password'] == $password) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];

        // Cek apakah user sudah punya data di tabel pekerja
        $checkPekerja = mysqli_query($conn, "SELECT * FROM pekerja WHERE user_id = " . $user['id']);

        if (mysqli_num_rows($checkPekerja) === 0) {
            // Insert default data pekerja jika belum ada
            $nama = mysqli_real_escape_string($conn, $username);
            $posisi = 'Belum diatur';
            $gaji = 0;
            $status = 'Aktif';

            mysqli_query($conn, "INSERT INTO pekerja (nama, posisi, gaji, status, user_id) 
                VALUES ('$nama', '$posisi', $gaji, '$status', " . $user['id'] . ")");
        }

        header("Location: admin.php");
        exit;
    } else {
        $pesan = "Username atau password salah!";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    
    <?php if (!empty($pesan)): ?>
  <div style="
    text-align: center;
    padding: 12px 20px;
    margin: 20px auto;
    border-radius: 6px;
    max-width: 90%;
    font-size: 14px;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: <?= strpos($pesan, 'berhasil') !== false ? '#d1fae5' : '#fee2e2' ?>;
    color: <?= strpos($pesan, 'berhasil') !== false ? '#065f46' : '#991b1b' ?>;
    border: 1px solid <?= strpos($pesan, 'berhasil') !== false ? '#10b981' : '#ef4444' ?>;
  ">
    <?= $pesan ?>
  </div>
<?php endif; ?>

        <header class="header">
            <h1>StaffHub</h1>   
        </header>


    <div class="container">
        <div class="body-left">
            <img src="left.png" alt="gambar awal">
        </div>
        <div class="body-right">
            <div class="bg">
                <div class="input">
                    <form action="login.php" method="POST">
                        <h2>LOGIN</h2>
                        <h5>Username</h5>
                        <input type="text" id="username" name="username" placeholder="username" required>

                        <h5>Password</h5>
                        <input type="password" id="password" name="password" placeholder="Password" required><br>

                        <input type="submit" class="btn" name="LOGIN" value="LOGIN">

                        <a href="reset.php">Forgot Password?</a>
                        <a href="register.php">Create New Account</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

