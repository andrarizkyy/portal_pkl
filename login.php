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
        header("Location: admin/index.php");
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
            header("Location: user/index.php");
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