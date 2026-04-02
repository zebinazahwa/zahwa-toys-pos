<?php
// ======= FILE: login.php =======
// Halaman login admin dengan desain premium.

session_start();
require_once 'backend/koneksi.php';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Login sukses
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Kata sandi salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Zahwa Toys</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6367FF;
            --primary-light: #8494FF;
            --secondary: #FF63A5;
            --dark: #1A1A2E;
            --glass: rgba(255, 255, 255, 0.9);
            --shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #6367FF 0%, #8494FF 100%);
            height: 100 vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--dark);
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .card {
            background: var(--glass);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 24px;
            padding: 40px;
            box-shadow: var(--shadow);
            text-align: center;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--primary);
            letter-spacing: -1px;
        }

        .logo span {
            color: var(--secondary);
        }

        .welcome-text {
            margin-bottom: 30px;
            color: #666;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 14px 20px;
            border-radius: 12px;
            border: 2px solid #EEE;
            background: #F9F9F9;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: #FFF;
            box-shadow: 0 0 0 4px rgba(99, 103, 255, 0.1);
        }

        .btn-login {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(99, 103, 255, 0.3);
        }

        .btn-login:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 103, 255, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-message {
            background: #FEE2E2;
            color: #DC2626;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border-left: 4px solid #DC2626;
            display: <?php echo $error ? 'block' : 'none'; ?>;
            text-align: left;
        }

        /* Micro-animations */
        .form-group:nth-child(2) { animation-delay: 0.1s; }
        .form-group:nth-child(3) { animation-delay: 0.2s; }
        .btn-login { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="logo">ZAHWA<span>TOYS</span></div>
            <p class="welcome-text">Silakan login untuk mengakses dashboard manajemen.</p>
            
            <?php if ($error): ?>
            <div class="error-message">
                <strong>Gagal!</strong> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi" required>
                </div>
                <button type="submit" class="btn-login">Masuk Sekarang</button>
            </form>
        </div>
    </div>
</body>
</html>
