<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard PKL Hub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="body.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="nav-left">
    <h2 class="logo">PKL Hub</h2>
    <ul class="nav-menu">
      <li>Dashboard</li>
      <li>Cari Tempat PKL</li>
      <li>Status Lamaran</li>
      <li>Riwayat PKL</li>
    </ul>
  </div>
  <div class="nav-right">
    <span class="user">Budi Santoso ⌄</span>
  </div>
</nav>

<!-- MAIN -->
<main class="container">

  <!-- HERO -->
  <section class="hero">
    <h1>Halo, <b>Budi Santoso</b> 👋</h1>
    <p>Yuk, cari & lamar tempat PKL terbaik untukmu!</p>

    <div class="hero-cards">

    <div class="search-box">
      <input type="text" placeholder="Cari posisi / perusahaan...">
      <button>Cari Tempat PKL</button>
    </div>

    <div class="category">
      <span>IT & Software</span>
      <span>Bisnis</span>
      <span>Teknik</span>
      <span>Kesehatan</span>
      <span>Akuntansi</span>
    </div>
    </div>
  </section>

  <!-- CONTENT -->
  <section class="content">

    <!-- LEFT -->
    <div class="left">

      <h3>Rekomendasi IT & Software</h3>

      <div class="cards">

        <div class="job-card">
          <h4>SunTech Corp</h4>
          <p>Programmer Intern • Jakarta</p>
          <button>Lamar</button>
        </div>

        <div class="job-card">
          <h4>Mitra Digital Indonesia</h4>
          <p>Web Developer Intern • Bandung</p>
          <button>Lamar</button>
        </div>

        <div class="job-card">
          <h4>Buana Teknologi</h4>
          <p>IT Support • Bogor</p>
          <button>Lamar</button>
        </div>

      </div>

      <!-- STATUS -->
      <h3>Status Lamaran</h3>
      <div class="table">
        <div class="row head">
          <span>Perusahaan</span>
          <span>Posisi</span>
          <span>Status</span>
        </div>

        <div class="row">
          <span>SunTech Corp</span>
          <span>Programmer</span>
          <span class="accepted">Diterima</span>
        </div>

        <div class="row">
          <span>Buana Teknologi</span>
          <span>IT Support</span>
          <span class="pending">Menunggu</span>
        </div>

        <div class="row">
          <span>Inti Data Solutions</span>
          <span>Web Designer</span>
          <span class="rejected">Ditolak</span>
        </div>
      </div>

    </div>

    <!-- RIGHT -->
    <div class="right">
      <div class="side-card">
        <h4>Riwayat PKL</h4>
        <p>Belum ada riwayat PKL</p>
      </div>
    </div>

  </section>

</main>

</body>
</html>
