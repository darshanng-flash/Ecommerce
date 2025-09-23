<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT SUM(p.price * c.quantity) AS total 
                      FROM cart c 
                      JOIN products p ON c.product_id = p.id 
                      WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$total = $stmt->fetchColumn();

if ($total <= 0) {
    header("Location: cart.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];

    if ($payment_method === 'card') {
        $card_name = $_POST['card_name'];
        $card_number = $_POST['card_number'];
        $expiry_date = $_POST['expiry_date'];
        $cvv = $_POST['cvv'];


        $stmt = $pdo->prepare("SELECT * FROM cards WHERE card_name = ? AND card_number = ? AND expiry_date = ? AND cvv = ?");
        $stmt->execute([$card_name, $card_number, $expiry_date, $cvv]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);

            
            header("Location: order_success.php?payment=card");
            exit();
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }

        .payment-container {
            background: white;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .card-input {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }

        .card-input input,
        select {
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }

        .card-input input:focus,
        select:focus {
            border-color: #6e45e2;
            box-shadow: 0 0 10px rgba(110, 69, 226, 0.5);
            outline: none;
        }

        .pay-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #6e45e2, #88d3ce);
            border: none;
            color: white;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .pay-btn:hover {
            background: linear-gradient(135deg, #88d3ce, #6e45e2);
            transform: scale(1.05);
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .total-info {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }

        .total-info strong {
            font-size: 22px;
            color: #6e45e2;
        }

        #cardFields {
            display: none;
        }
    </style>
</head>
<body>

<div class="payment-container">
    <h2>Secure Payment</h2>
    <p class="total-info">Total: <strong>₹<?= number_format($total, 2) ?></strong></p>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="card-input">
            <label for="payment_method">Select Payment Method:</label>
            <select name="payment_method" id="payment_method" required onchange="togglePaymentFields()">
                <option value="card">Credit/Debit Card</option>
                <option value="upi">UPI</option>
                <option value="netbanking">Net Banking</option>
                <option value="cod">Cash on Delivery</option>
            </select>

            <div id="cardFields">
                <input type="text" name="card_name" placeholder="Cardholder Name">
                <input type="text" name="card_number" placeholder="Card Number" maxlength="16">
                <input type="text" name="expiry_date" placeholder="MM/YY" maxlength="5">
                <input type="text" name="cvv" placeholder="CVV" maxlength="3">
            </div>
        </div>

        <button type="submit" class="pay-btn">Pay Now</button>
    </form>
</div>

<script>
    function togglePaymentFields() {
        const method = document.getElementById("payment_method").value;
        const cardFields = document.getElementById("cardFields");
        const inputs = cardFields.querySelectorAll('input');

        if (method === 'card') {
            cardFields.style.display = 'block';
            inputs.forEach(i => i.required = true);
        } else {
            cardFields.style.display = 'none';
            inputs.forEach(i => i.required = false);
        }
    }

    window.onload = togglePaymentFields;
</script>

</body>
</html>
