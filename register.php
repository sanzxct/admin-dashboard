<?php
session_start();
include 'koneksi.php';

    $pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST'  && isset($_POST['REGISTER'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];


    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($query) > 0) {
        $pesan = "Username sudah terdaftar!";
    } else {

        $insert = mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$username', '$password')");
        if ($insert) {
            $pesan = "Registrasi berhasil! Silakan login.";
        } else {
            $pesan = "kesalahan saat registrasi: " . mysqli_error($conn);
        }
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
                    <form action="register.php" method="POST">
                    <h2>REGISTER</h2>
                    <h5>Username</h5>
                    <input type="text" id="username" name="username" placeholder="username" required>
                    <h5>Password</h5>
                    <input type="password" id="password" name="password" placeholder="Password" required> <br>

                    <input type="submit" name="REGISTER" class="btn" value="REGISTER">

                    <a href="login.php">Back to login</a>
                    </form>


                </div>
            </div>

        </div>
    </div>
</body>
</html>