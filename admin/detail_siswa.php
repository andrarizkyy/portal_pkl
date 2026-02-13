<?php
include "../config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$nis = $_GET['nis'] ?? '';
if(!$nis) die("Siswa tidak ditemukan.");

/* ================= DATA SISWA ================= */
$q = mysqli_query($conn,"
    SELECT 
        s.*,
        k.nama_kelas,
        j.nama_jurusan,
        d.nama_dudi,
        d.bidang_usaha,
        d.no_hp_dudi,
        d.alamat_dudi
    FROM siswa s
    LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
    LEFT JOIN jurusan j ON k.id_jurusan = j.id_jurusan
    LEFT JOIN dudi d ON s.id_dudi = d.id_dudi
    WHERE s.nis = '$nis'
");

if(mysqli_num_rows($q) == 0) die("Data tidak ditemukan.");
$s = mysqli_fetch_assoc($q);

$tgl_lahir = $s['TTL']
    ? date('d/m/Y', strtotime($s['TTL']))
    : '-';
?>

<!DOCTYPE html>
<html>
<head>
<title>Detail Siswa</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins', sans-serif;}
body{display:flex;background:#f3f4f6;}

/* SIDEBAR */
.sidebar{
    width:260px;background:white;min-height:100vh;
    padding:30px 20px;border-right:1px solid #e5e7eb;
}
.sidebar h2{
    color:#8b5cf6;margin-bottom:40px;
    display:flex;align-items:center;gap:10px;
}
.sidebar a{
    display:flex;align-items:center;gap:12px;
    padding:12px 15px;text-decoration:none;
    color:#374151;margin-bottom:8px;
    border-radius:8px;transition:0.2s;font-size:14px;
}
.sidebar a i{width:18px;}
.sidebar a:hover{background:#f3f4f6;color:#8b5cf6;}
.sidebar a.active{
    background:#ede9fe;color:#6d28d9;font-weight:600;position:relative;
}
.sidebar a.active::before{
    content:"";position:absolute;left:0;top:0;bottom:0;
    width:4px;background:#8b5cf6;border-radius:4px 0 0 4px;
}

/* MAIN */
.main{flex:1;}

/* TOPBAR */
.topbar{
    height:70px;background:white;
    display:flex;align-items:center;justify-content:flex-end;
    padding:0 30px;border-bottom:1px solid #e5e7eb;
}
.profile{
    font-weight:600;display:flex;align-items:center;gap:8px;
}

/* CONTENT */
.content{padding:30px;}

/* CARD */
.card{
    background:white;padding:25px;margin-bottom:30px;
    border-radius:14px;border:1px solid #e5e7eb;
}

.card h3{
    margin-bottom:15px;
}

/* HERO */
.hero h1{
    font-size:24px;margin-bottom:5px;
}

.hero p{
    color:#6b7280;margin-bottom:15px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.label{
    font-size:13px;
    color:#6b7280;
}

.value{
    font-size:14px;
    font-weight:600;
    margin-top:5px;
}

/* BUTTON */
.btn{
    display:inline-block;
    padding:8px 16px;
    background:#1877F2;
    color:white;
    text-decoration:none;
    font-size:14px;
    border-radius:6px;
    margin-bottom:20px;
}
.btn:hover{
    background:#0f5dc2;
}

.alert{
    padding:15px;
    background:#fef9c3;
    border-radius:8px;
}
</style>
</head>

<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> Portal PKL</h2>

    <a href="home.php">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="siswa.php" class="active">
        <i class="fa-solid fa-user-graduate"></i> Data Siswa
    </a>

    <a href="dudi.php">
        <i class="fa-solid fa-building"></i> Data DUDI
    </a>
</div>

<div class="main">

<div class="topbar">
    <div class="profile">
        <i class="fa-solid fa-user-shield"></i> Admin
    </div>
</div>

<div class="content">

<a href="siswa.php" class="btn">← Kembali</a>

<!-- HERO -->
<div class="card hero">
    <h1><?= $s['nama_siswa']; ?></h1>
    <p>
        NIS: <?= $s['nis']; ?> • 
        <?= $s['nama_jurusan'] ?? '-'; ?> • 
        <?= $s['nama_kelas'] ?? '-'; ?>
    </p>
</div>

<!-- DATA SISWA -->
<div class="card">
    <h3>Data Siswa</h3>

    <div class="grid">

        <div>
            <div class="label">Jenis Kelamin</div>
            <div class="value"><?= $s['jenis_kelamin']; ?></div>
        </div>

        <div>
            <div class="label">Agama</div>
            <div class="value"><?= $s['agama']; ?></div>
        </div>

        <div>
            <div class="label">Tanggal Lahir</div>
            <div class="value"><?= $tgl_lahir; ?></div>
        </div>

        <div>
            <div class="label">No HP</div>
            <div class="value"><?= $s['no_hp']; ?></div>
        </div>

        <div style="grid-column:1/-1">
            <div class="label">Alamat</div>
            <div class="value"><?= $s['alamat']; ?></div>
        </div>

    </div>
</div>

<!-- DATA DUDI -->
<div class="card">
    <h3>Tempat PKL (DUDI)</h3>

    <?php if($s['id_dudi']){ ?>
        <div class="grid">
            <div>
                <div class="label">Nama DUDI</div>
                <div class="value"><?= $s['nama_dudi']; ?></div>
            </div>

            <div>
                <div class="label">Bidang Usaha</div>
                <div class="value"><?= $s['bidang_usaha']; ?></div>
            </div>

            <div>
                <div class="label">No HP DUDI</div>
                <div class="value"><?= $s['no_hp_dudi']; ?></div>
            </div>

            <div style="grid-column:1/-1">
                <div class="label">Alamat DUDI</div>
                <div class="value"><?= $s['alamat_dudi']; ?></div>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert">
            Siswa ini belum ditempatkan di DUDI.
        </div>
    <?php } ?>

</div>

</div>
</div>

</body>
</html>
