<?php
session_start();
include 'koneksi.php';

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['RESET'])) {
    $username = $_POST['username'];
    $newPass = $_POST['NewPassword'];
    $confirmPass = $_POST['ConfirmPassword'];

    if ($newPass !== $confirmPass) {
        $pesan = "Password tidak cocok!";
    } else {
        $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($query) > 0) {
            $update = mysqli_query($conn, "UPDATE users SET password = '$newPass' WHERE username = '$username'");
            if ($update) {
                $pesan = "Password berhasil diubah! Silakan login.";
            } else {
                $pesan = "Kesalahan saat mengubah password: " . mysqli_error($conn);
            }
        } else {
            $pesan = "Username tidak ditemukan!";
        }
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
                <form action="reset.php" method="POST">
                    <h2>RESET PASSWORD</h2>
                    <h5>Username</h5>
                    <input type="text" id="username" name="username" placeholder="Username" required>

                    <h5>New Password</h5>
                    <input type="text" id="newpassword" name="NewPassword" placeholder="New Password" required>

                    <h5>Confirm Password</h5>
                    <input type="password" id="confirmpassword" name="ConfirmPassword" placeholder="Confirm Password" required> <br>

                    <input type="submit" name="RESET" class="btn" value="RESET">

                    <a href="login.php">Back to login</a>
                    <a href="register.php">Create New Account</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
