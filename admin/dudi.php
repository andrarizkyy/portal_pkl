<?php
include "../config.php";

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================== DATA DUDI (TIDAK DIUBAH) ================== */
$data_dudi = mysqli_query($conn,"
    SELECT
        d.*,
        COUNT(s.nis) AS jumlah_siswa
    FROM dudi d
    LEFT JOIN siswa s ON d.id_dudi = s.id_dudi
    GROUP BY d.id_dudi
    ORDER BY d.nama_dudi
");

$total_dudi = mysqli_num_rows($data_dudi);
?>

<!DOCTYPE html>
<html>
<head>
<title>Data DUDI</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* ================= GLOBAL ================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    display:flex;
    background:#f3f4f6;
}

/* ================= SIDEBAR (SAMA PERSIS) ================= */

.sidebar{
    width:260px;
    background:white;
    min-height:100vh;
    padding:30px 20px;
    border-right:1px solid #e5e7eb;
}

.sidebar h2{
    color:#8b5cf6;
    margin-bottom:40px;
    display:flex;
    align-items:center;
    gap:10px;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 15px;
    text-decoration:none;
    color:#374151;
    margin-bottom:8px;
    border-radius:8px;
    transition:0.2s;
    font-size:14px;
}

.sidebar a i{
    width:18px;
}

.sidebar a:hover{
    background:#f3f4f6;
    color:#8b5cf6;
}

.sidebar a.active{
    background:#ede9fe;
    color:#6d28d9;
    font-weight:600;
    position:relative;
}

.sidebar a.active::before{
    content:"";
    position:absolute;
    left:0;
    top:0;
    bottom:0;
    width:4px;
    background:#8b5cf6;
    border-radius:4px 0 0 4px;
}

/* ================= MAIN ================= */

.main{
    flex:1;
}

/* ================= TOPBAR (SAMA PERSIS) ================= */

.topbar{
    height:70px;
    background:white;
    display:flex;
    align-items:center;
    justify-content:flex-end;
    padding:0 30px;
    border-bottom:1px solid #e5e7eb;
}

.profile{
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
}

/* ================= CONTENT ================= */

.content{
    padding:30px;
}

/* ================= CARDS (SAMA PERSIS) ================= */

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
    margin-bottom:40px;
}

.card{
    padding:30px;
    color:white;
    position:relative;
    overflow:hidden;
    border-radius:14px;
}

.card h3{
    font-weight:400;
    margin-bottom:10px;
    display:flex;
    align-items:center;
    gap:8px;
}

.card p{
    font-size:30px;
    font-weight:600;
}

.card::after{
    content:"";
    position:absolute;
    width:180px;
    height:180px;
    background:rgba(255,255,255,0.15);
    border-radius:50%;
    top:-40px;
    right:-40px;
}

.card.dudi{
    background:linear-gradient(135deg,#60a5fa,#3b82f6);
}

/* ================= SECTION (SAMA PERSIS) ================= */

.section{
    background:white;
    padding:25px;
    margin-bottom:30px;
    border-radius:14px;
    border:1px solid #e5e7eb;
}

.section h3{
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:8px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    padding:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:14px;
}

thead{
    background:#f9fafb;
}

tr:hover{
    background:#f3f4f6;
}

/* BUTTON (SAMA PERSIS) */

.btn{
    display:inline-block;
    margin-top:15px;
    padding:8px 16px;
    background:#1877F2;
    color:white;
    text-decoration:none;
    font-size:14px;
    border-radius:6px;
}

.btn:hover{
    background:#0f5dc2;
}

</style>
</head>

<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> Portal PKL</h2>

    <a href="home.php">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="siswa.php">
        <i class="fa-solid fa-user-graduate"></i> Data Siswa
    </a>

    <a href="dudi.php" class="active">
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

        <!-- CARD TOTAL -->
        <div class="cards">
            <div class="card dudi">
                <h3><i class="fa-solid fa-building"></i> Total DUDI</h3>
                <p><?= $total_dudi ?></p>
            </div>
        </div>

        <!-- DATA DUDI -->
        <div class="section">
            <h3><i class="fa-solid fa-briefcase"></i> Data Dunia Usaha & Dunia Industri</h3>

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
                    <?php mysqli_data_seek($data_dudi, 0); ?>
                    <?php while($d=mysqli_fetch_assoc($data_dudi)): ?>
                    <tr>
                        <td><?= $d['nama_dudi']; ?></td>
                        <td><?= $d['bidang_usaha']; ?></td>
                        <td><?= $d['jumlah_siswa']; ?></td>
                        <td>
                            <a href="detail_dudi.php?id_dudi=<?= $d['id_dudi']; ?>" class="btn">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>

    </div>

</div>

</body>
</html>
