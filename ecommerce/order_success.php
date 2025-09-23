<?php
session_start();
require 'config.php';

$payment_method = $_GET['payment'] ?? '';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($payment_method == 'cod') {
    $message = "Thank you for shopping with us! Your order will be delivered soon.";
} else if ($payment_method == 'card') {
    $message = "Thank you for your payment! Your order is confirmed.";
} else {
    $message = "Thank you for shopping with us!";
}

header("refresh:5; url=index.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6e45e2, #88d3ce);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            overflow: hidden;
        }

        .success-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 90%;
            max-width: 600px;
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: 40px;
            color: #6e45e2;
            margin-bottom: 20px;
        }

        p {
            font-size: 20px;
            color: #333;
            margin-bottom: 40px;
        }

        .celebration {
            font-size: 100px;
            color: #6e45e2;
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            0% { transform: translateY(0); }
            100% { transform: translateY(-10px); }
        }

        .congrats-message {
            font-size: 18px;
            color: #333;
        }

        .return-btn {
            padding: 15px 30px;
            background-color: #6e45e2;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .return-btn:hover {
            background-color: #88d3ce;
        }

        .confetti {
            position: absolute;
            top: -20px;
            left: 50%;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .confetti div {
            position: absolute;
            background: #ff9f00;
            width: 5px;
            height: 5px;
            opacity: 0.8;
            animation: confetti-fall 2s linear infinite;
        }

        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }

        .confetti div:nth-child(1) { left: 10%; animation-duration: 2.5s; animation-delay: 0.2s; }
        .confetti div:nth-child(2) { left: 20%; animation-duration: 3s; animation-delay: 0.3s; }
        .confetti div:nth-child(3) { left: 30%; animation-duration: 3.5s; animation-delay: 0.1s; }
        .confetti div:nth-child(4) { left: 40%; animation-duration: 2s; animation-delay: 0.4s; }
        .confetti div:nth-child(5) { left: 50%; animation-duration: 3s; animation-delay: 0.6s; }
        .confetti div:nth-child(6) { left: 60%; animation-duration: 2.5s; animation-delay: 0.3s; }
        .confetti div:nth-child(7) { left: 70%; animation-duration: 3.5s; animation-delay: 0.2s; }
        .confetti div:nth-child(8) { left: 80%; animation-duration: 2s; animation-delay: 0.5s; }
    </style>
</head>
<body>

<div class="confetti">

    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
</div>

<div class="success-container">
    <h1>Order Successful!</h1>
    <div class="celebration">🎉</div>
    <p><?= $message ?></p>
    <button class="return-btn" onclick="window.location.href='index.php'">Return to Home</button>
</div>

<script>
   
    const confettiContainer = document.querySelector('.confetti');
    for (let i = 0; i < 50; i++) {
        let confettiPiece = document.createElement('div');
        confettiContainer.appendChild(confettiPiece);
    }
</script>

</body>
</html>
