<?php
session_start();

// If user is already logged in, redirect to index
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "utsavtkdm";

$error_message = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_username = trim($_POST['username']);
    $input_password = trim($_POST['password']);
    
    if (!empty($input_username) && !empty($input_password)) {
        try {
            // Create connection
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check admin credentials
            $login_query = "SELECT slno, username FROM admin WHERE username = ? AND password = ?";
            $login_stmt = $pdo->prepare($login_query);
            $login_stmt->execute([$input_username, $input_password]);
            $admin = $login_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['slno'];
                $_SESSION['admin_username'] = $admin['username'];
                
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Invalid username or password!";
            }
            
        } catch(PDOException $e) {
            $error_message = "Database connection failed: " . $e->getMessage();
        }
    } else {
        $error_message = "Please enter both username and password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="style.css">
        
    <!-- ===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <title>Login - Utsav TKDM</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--body-color);
            background-image: url("Photos/YD_Logo_BG.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Dark mode container */
        body.dark .login-container {
            background-color: rgba(36, 37, 38, 0.95);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        
        .login-header {
            margin-bottom: 30px;
        }
        
        .login-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        
        .login-header h1 {
            color: var(--search-text);
            margin: 0;
            font-size: 24px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        body.dark .login-header h1 {
            color: var(--text-color);
        }
        
        .login-header p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        body.dark .login-header p {
            color: #aaa;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            position: relative;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--search-text);
            font-weight: 500;
            font-size: 14px;
        }

        body.dark .form-group label {
            color: var(--text-color);
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.8);
            box-sizing: border-box;
        }

        body.dark .form-group input {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            color: var(--text-color);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
            background-color: rgba(255, 255, 255, 1);
        }

        body.dark .form-group input:focus {
            border-color: #64B5F6;
            box-shadow: 0 0 0 3px rgba(100, 181, 246, 0.1);
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .form-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
        }

        body.dark .form-group i {
            color: #aaa;
        }
        
        .login-btn {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        body.dark .login-btn {
            background: linear-gradient(135deg, #64B5F6, #42A5F5);
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }

        body.dark .login-btn:hover {
            box-shadow: 0 5px 15px rgba(100, 181, 246, 0.4);
        }
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        .login-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }

        body.dark .error-message {
            background-color: rgba(244, 67, 54, 0.1);
            color: #ff6b6b;
            border-color: rgba(244, 67, 54, 0.2);
        }
        
        .dark-light-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        body.dark .dark-light-toggle {
            background: rgba(36, 37, 38, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .dark-light-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        body.dark .dark-light-toggle:hover {
            background: rgba(36, 37, 38, 0.9);
        }
        
        .dark-light-toggle i {
            font-size: 24px;
            color: var(--search-text);
            transition: all 0.3s ease;
        }

        body.dark .dark-light-toggle i {
            color: var(--text-color);
        }
        
        /* Hide sun in light mode, hide moon in dark mode */
        .sun {
            display: none;
        }
        
        body.dark .sun {
            display: block;
        }
        
        body.dark .moon {
            display: none;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(0,0,0,0.1);
            color: #666;
            font-size: 12px;
        }

        body.dark .footer {
            border-top-color: rgba(255,255,255,0.1);
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="dark-light-toggle">
        <i class='bx bx-moon moon'></i>
        <i class='bx bx-sun sun'></i>
    </div>

    <div class="login-container">
        <div class="login-header">
            <img src="Photos/YD_Logo_BG.png" alt="UTSAV TKDM Logo">
            <h1>UTSAV TKDM</h1>
        
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <i class='bx bx-error'></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form class="login-form" method="POST">
            <div class="form-group">
                <label for="username">
                    <i class='bx bx-user'></i>
                    Username
                </label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       placeholder="Enter your username">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class='bx bx-lock-alt'></i>
                    Password
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="Enter your password">
            </div>
            
            <button type="submit" class="login-btn">
                <i class='bx bx-log-in'></i>
                Login
            </button>
        </form>
        
        <div class="footer">
            <p>&copy; 2025 UTSAV TKDM. Secure Admin Access.</p>
        </div>
    </div>

    <script>
        // Dark mode functionality
        const body = document.querySelector("body");
        const darkLightToggle = document.querySelector(".dark-light-toggle");

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            body.classList.add('dark');
        }

        darkLightToggle.addEventListener("click", () => {
            body.classList.toggle("dark");
            
            // Save theme preference
            if (body.classList.contains("dark")) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });

        // Form validation and enhancement
        const form = document.querySelector('.login-form');
        const loginBtn = document.querySelector('.login-btn');
        const inputs = document.querySelectorAll('input');

        // Add loading state on form submission
        form.addEventListener('submit', function() {
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Logging in...';
        });

        // Focus enhancement
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentNode.style.transform = 'scale(1)';
            });
        });

        // Enter key to submit
        inputs.forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>