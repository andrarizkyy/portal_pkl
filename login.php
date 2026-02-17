<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM perusahaan WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status_akun'] !== 'aktif') {
                    $error = 'Akun Anda belum aktif atau sedang ditangguhkan.';
                } else {
                    // Login berhasil
                    $_SESSION['id_perusahaan']   = $user['id_perusahaan'];
                    $_SESSION['nama_perusahaan'] = $user['nama_perusahaan'];
                    $_SESSION['email']           = $user['email'];
                    $_SESSION['role']            = 'perusahaan';

                    // Langsung redirect ke dashboard
                    header("Location: dashboard.php");
                    exit; // penting! agar script tidak lanjut
                }
            } else {
                $error = 'Email atau password salah.';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Perusahaan - Portal PKL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 15px 40px rgba(0,0,0,0.25);
      max-width: 420px;
      width: 100%;
      overflow: hidden;
    }
    .login-header {
      background: #1e40af;
      color: white;
      padding: 2.5rem;
      text-align: center;
    }
    .login-body {
      padding: 2.5rem;
    }
    .btn-login {
      background: #1e40af;
      border: none;
      width: 100%;
      padding: 0.8rem;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="login-card">
  <div class="login-header">
    <h1>PKL Hub</h1>
    <p>Login Perusahaan</p>
  </div>

  <div class="login-body">
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required 
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary btn-login">Masuk</button>
    </form>
  </div>
</div>

</body>
</html>