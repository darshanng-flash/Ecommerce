<?php
require 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? ''; 
    $phone    = $_POST['phone'] ?? '';
    $address  = $_POST['address'] ?? '';
    $pincode  = $_POST['pincode'] ?? '';

   
    if (empty($username) || empty($email) || empty($password) || empty($phone) || empty($address) || empty($pincode)) {
        $_SESSION['error'] = "Please fill in all the fields.";
        header("Location: register.php");
        exit();
    }

    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $_SESSION['error'] = "Email already registered!";
        header("Location: register.php");
        exit();
    }

    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin, phone, address, pincode)
                           VALUES (?, ?, ?, 0, ?, ?, ?)");
    $success = $stmt->execute([$username, $email, $password, $phone, $address, $pincode]);

    if ($success) {
        $_SESSION['message'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: register.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - TechZone</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4b0082, #0000ff);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }

        .form-container h2 {
            margin-bottom: 20px;
        }

        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background: #00ffcc;
            border: none;
            color: black;
            font-size: 16px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-container button:hover {
            background: #00bfa5;
        }

        .form-container p {
            margin-top: 15px;
        }

        .form-container .error {
            background-color: #ff4c4c;
            padding: 10px;
            border-radius: 5px;
            color: white;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Create Account</h2>
    <?php if(isset($_SESSION['error'])): ?>
        <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="User Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="password" placeholder="Password" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="text" name="address" placeholder="Full Address" required>
        <input type="text" name="pincode" placeholder="Pincode" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php" style="color:#00ffcc;">Login</a></p>
</div>

</body>
</html>
