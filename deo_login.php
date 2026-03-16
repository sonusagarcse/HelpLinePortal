<?php
session_start();
require_once('connection.php');
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEO Login - Yuva Helpline</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 50px;
            color: #1e3c72;
            margin-bottom: 15px;
        }
        .login-header h2 {
            font-weight: 700;
            color: #333;
        }
        .btn-login {
            background: #1e3c72;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: #2a5298;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-keyboard"></i>
            <h2>DEO Panel</h2>
            <p class="text-muted">Enter credentials to continue</p>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'error'): ?>
            <div class="alert alert-danger">Invalid Registration Number or Password.</div>
        <?php endif; ?>

        <form action="code/deo_login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3">
                <label class="form-label">Reg. Number / Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="regno" class="form-control" placeholder="DEO0001" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="********" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-login">Login to Panel</button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <a href="index.php" class="text-decoration-none text-muted">
                <i class="fas fa-arrow-left me-1"></i> Back to Website
            </a>
        </div>
    </div>
</body>
</html>
