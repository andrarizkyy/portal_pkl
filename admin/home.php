<?php
include "../config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================== QUERY ================== */

$total_siswa   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM siswa"))['t'];
$total_dudi    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM dudi"))['t'];
$total_jurusan = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM jurusan"))['t'];

/* ===== DATA DUDI + JUMLAH SISWA (MAX 3) ===== */
$data_dudi = mysqli_query($conn,"
    SELECT
        d.id_dudi,
        d.nama_dudi,
        d.bidang_usaha,
        COUNT(s.nis) AS jumlah_siswa
    FROM dudi d
    LEFT JOIN siswa s ON d.id_dudi = s.id_dudi
    GROUP BY d.id_dudi
    ORDER BY d.nama_dudi
    LIMIT 3
");

/* ===== DATA SISWA + NIS + DUDI (MAX 3) ===== */
$data_siswa = mysqli_query($conn,"
    SELECT 
        s.nis,
        s.nama_siswa,
        k.nama_kelas,
        d.nama_dudi
    FROM siswa s
    LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
    LEFT JOIN dudi d ON s.id_dudi = d.id_dudi
    ORDER BY s.nama_siswa
    LIMIT 3
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins', sans-serif;}
body{display:flex;background:#f3f4f6;}

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

.main{flex:1;}

.topbar{
    height:70px;background:white;
    display:flex;align-items:center;justify-content:flex-end;
    padding:0 30px;border-bottom:1px solid #e5e7eb;
}
.profile{font-weight:600;display:flex;align-items:center;gap:8px;}

.content{padding:30px;}

.cards{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;margin-bottom:40px;
}
.card{
    padding:30px;color:white;position:relative;
    overflow:hidden;border-radius:14px;
}
.card h3{
    font-weight:400;margin-bottom:10px;
    display:flex;align-items:center;gap:8px;
}
.card p{font-size:30px;font-weight:600;}
.card::after{
    content:"";position:absolute;width:180px;height:180px;
    background:rgba(255,255,255,0.15);
    border-radius:50%;top:-40px;right:-40px;
}
.card.siswa{background:linear-gradient(135deg,#ff9a9e,#f472b6);}
.card.dudi{background:linear-gradient(135deg,#60a5fa,#3b82f6);}
.card.jurusan{background:linear-gradient(135deg,#34d399,#10b981);}

.section{
    background:white;padding:25px;margin-bottom:30px;
    border-radius:14px;border:1px solid #e5e7eb;
}
.section h3{
    margin-bottom:20px;display:flex;
    align-items:center;gap:8px;
}
table{width:100%;border-collapse:collapse;}
th,td{
    padding:12px;border-bottom:1px solid #e5e7eb;
    text-align:left;font-size:14px;
}
thead{background:#f9fafb;}
tr:hover{background:#f3f4f6;}

.btn{
    display:inline-block;padding:6px 12px;
    background:#1877F2;color:white;
    text-decoration:none;font-size:13px;
    border-radius:6px;
}
.btn:hover{background:#0f5dc2;}
</style>
</head>

<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> Portal PKL</h2>

    <a href="home.php" class="active">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>
    <a href="siswa.php">
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

<div class="cards">
    <div class="card siswa">
        <h3><i class="fa-solid fa-users"></i> Total Siswa</h3>
        <p><?= $total_siswa ?></p>
    </div>

    <div class="card dudi">
        <h3><i class="fa-solid fa-building"></i> Total DUDI</h3>
        <p><?= $total_dudi ?></p>
    </div>

    <div class="card jurusan">
        <h3><i class="fa-solid fa-layer-group"></i> Total Jurusan</h3>
        <p><?= $total_jurusan ?></p>
    </div>
</div>

<!-- DATA DUDI -->
<div class="section">
    <h3><i class="fa-solid fa-briefcase"></i> Data DUDI</h3>
    <table>
        <thead>
            <tr>
                <th>Nama DUDI</th>
                <th>Bidang Usaha</th>
                <th>Jumlah Siswa PKL</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($d = mysqli_fetch_assoc($data_dudi)): ?>
            <tr>
                <td><?= $d['nama_dudi'] ?></td>
                <td><?= $d['bidang_usaha'] ?></td>
                <td><?= $d['jumlah_siswa'] ?></td>
                <td>
                    <a href="detail_dudi.php?id_dudi=<?= $d['id_dudi'] ?>" class="btn">
                        Detail
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="dudi.php" class="btn" style="margin-top:15px;">Lihat Semua</a>
</div>

<!-- DATA SISWA -->
<div class="section">
    <h3><i class="fa-solid fa-user-graduate"></i> Data Siswa</h3>
    <table>
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>DUDI</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($s = mysqli_fetch_assoc($data_siswa)): ?>
            <tr>
                <td><?= $s['nis'] ?></td>
                <td><?= $s['nama_siswa'] ?></td>
                <td><?= $s['nama_kelas'] ?? '-' ?></td>
                <td><?= $s['nama_dudi'] ?? '-' ?></td>
                <td>
                    <a href="detail_siswa.php?nis=<?= $s['nis'] ?>" class="btn">
                        Detail
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="siswa.php" class="btn" style="margin-top:15px;">Lihat Semua</a>
</div>

</div>
</div>

</body>
</html>
