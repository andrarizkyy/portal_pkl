<?php
session_start();
include "config.php";

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    /* ================== ADMIN ================== */
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = 'admin';
        header("Location: admin/home.php");
        exit;
    }

    /* ================== SISWA (NIS) ================== */
    if ($password === '12345678') {
        $qSiswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis='$username'");
        if (mysqli_num_rows($qSiswa) > 0) {
            $s = mysqli_fetch_assoc($qSiswa);
            $_SESSION['role'] = 'siswa';
            $_SESSION['nis']  = $s['nis'];
            $_SESSION['nama'] = $s['nama_siswa'];
            header("Location: siswa/dashboard.php");
            exit;
        }

        /* ================== DUDI ================== */
        $qDudi = mysqli_query($conn, "SELECT * FROM dudi WHERE id_dudi='$username'");
        if (mysqli_num_rows($qDudi) > 0) {
            $d = mysqli_fetch_assoc($qDudi);
            $_SESSION['role'] = 'dudi';
            $_SESSION['id_dudi'] = $d['id_dudi'];
            $_SESSION['nama'] = $d['nama_dudi'];
            header("Location: dudi/dashboard.php");
            exit;
        }
    }

    $error = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login | Portal PKL</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    background:linear-gradient(135deg,#1e3c72,#2a5298);
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Segoe UI',sans-serif;
}
.login-card{
    background:rgba(255,255,255,.95);
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,.2);
    padding:35px;
    width:100%;
    max-width:420px;
}
.login-icon{
    width:80px;height:80px;
    background:linear-gradient(135deg,#2a5298,#6a11cb);
    color:#fff;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:36px;
    margin:-70px auto 20px;
    box-shadow:0 10px 20px rgba(0,0,0,.25);
}
.btn-login{
    background:linear-gradient(135deg,#1e3c72,#6a11cb);
    border:none;
}
.btn-login:hover{
    opacity:.9;
}
small.hint{
    color:#666;
}
</style>
</head>

<body>

<div class="login-card">
    <div class="login-icon">
        <i class="bi bi-mortarboard-fill"></i>
    </div>

    <h4 class="text-center mb-1">Portal PKL Sekolah</h4>
    <p class="text-center text-muted mb-4">Silakan login untuk melanjutkan</p>

    <?php if($error): ?>
        <div class="alert alert-danger text-center">
            <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="NIS / ID DUDI / admin">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="********">
        </div>

        <button name="login" class="btn btn-login text-white w-100 py-2">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </button>
    </form>

    <div class="text-center mt-3">
        <small class="hint">
            Siswa & DUDI: <b>12345678</b><br>
            Admin: <b>admin123</b>
        </small>
    </div>
</div>

</body>
</html>
