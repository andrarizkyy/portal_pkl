<?php
session_start();
include "../config.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);

/* ================== ADMIN ================== */
if ($username === 'admin' && $password === 'admin123') {
    $_SESSION['role']  = 'admin';
    $_SESSION['nama']  = 'Administrator';
    header("Location: admin/home.php");
    exit;
}

/* ================== SISWA ================== */
if ($password === '12345678') {
    $q_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis='$username'");
    if (mysqli_num_rows($q_siswa) === 1) {
        $s = mysqli_fetch_assoc($q_siswa);
        $_SESSION['role'] = 'siswa';
        $_SESSION['nis']  = $s['nis'];
        $_SESSION['nama'] = $s['nama_siswa'];
        header("Location: siswa/dashboard.php");
        exit;
    }
}

/* ================== DUDI ================== */
if ($password === '12345678') {
    $q_dudi = mysqli_query($conn, "SELECT * FROM dudi WHERE id_dudi='$username'");
    if (mysqli_num_rows($q_dudi) === 1) {
        $d = mysqli_fetch_assoc($q_dudi);
        $_SESSION['role']    = 'dudi';
        $_SESSION['id_dudi'] = $d['id_dudi'];
        $_SESSION['nama']    = $d['nama_dudi'];
        header("Location: dudi/dashboard.php");
        exit;
    }
}

/* ================== GAGAL ================== */
$_SESSION['error'] = "Username atau Password salah!";
header("Location: login.php");
exit;
