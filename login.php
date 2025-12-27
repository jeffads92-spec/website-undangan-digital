<?php
// login.php - Login untuk admin customer
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder = sanitize($_POST['website_folder']);
    $password = $_POST['password'];
    
    // Cari customer berdasarkan folder
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE website_folder = ?");
    $stmt->execute([$folder]);
    $customer = $stmt->fetch();
    
    if ($customer) {
        // Verify password
        if (password_verify($password, $customer['password_hash'])) {
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['website_folder'] = $customer['website_folder'];
            $_SESSION['customer_name'] = $customer['customer_name'];
            
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Website tidak ditemukan!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Wedding Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 3rem;
            color: #8B4513;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
        
        .form-control:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 0.25rem rgba(139, 69, 19, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #8B4513 0%, #C19A6B 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="logo">
                <i class="fas fa-heart"></i>
                <h2 class="mt-3">Login Admin</h2>
                <p class="text-muted">Masuk ke dashboard website undangan Anda</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Nama Folder Website</label>
                    <input type="text" name="website_folder" class="form-control" 
                           placeholder="Contoh: rina-budi-2024" required>
                    <small class="text-muted">Nama folder website Anda</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </form>
            
            <div class="back-link">
                <a href="index.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke halaman utama
                </a>
            </div>
            
            <div class="mt-4 text-center">
                <small class="text-muted">
                    Lupa password? Hubungi kami di WhatsApp: +62 812-3456-7890
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
