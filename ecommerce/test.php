<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 550px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        .info p {
            margin: 10px 0;
        }
        .method-label {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 10px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #218838;
        }
        .input-group {
            margin: 10px 0;
        }
        .input-group label {
            display: block;
            margin-bottom: 4px;
        }
        .input-group input {
            width: 100%;
            padding: 8px;
        }
    </style>
    <script>
        function showFields() {
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            const inputs = document.querySelectorAll('.payment-fields');
            inputs.forEach(div => div.style.display = 'none');

            const selectedDiv = document.getElementById(method + "-fields");
            if (selectedDiv) selectedDiv.style.display = 'block';
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Payment</h2>
    <div class="info">
        <p><strong>Movie:</strong> Joker</p>
        <p><strong>Seats:</strong> A2 (1 ticket)</p>
        <p><strong>Date:</strong> 2025-05-08</p>
        <p><strong>Time:</strong> 10:00 AM</p>
        <p><strong>Total:</strong> $10</p>
    </div>

    <form method="POST" action="thanku.php">
        <p><strong>Payment Method:</strong></p>
        <label class="method-label"><input type="radio" name="payment_method" value="card" onclick="showFields()" required> Card</label>
        <label class="method-label"><input type="radio" name="payment_method" value="upi" onclick="showFields()"> UPI</label>
        <label class="method-label"><input type="radio" name="payment_method" value="netbanking" onclick="showFields()"> Internet Banking</label>
        <label class="method-label"><input type="radio" name="payment_method" value="debit" onclick="showFields()"> Debit Card</label>

        <!-- Card Payment Fields -->
        <div id="card-fields" class="payment-fields" style="display:none;">
            <div class="input-group">
                <label>Cardholder Name:</label>
                <input type="text" name="card_name">
            </div>
            <div class="input-group">
                <label>Card Number:</label>
                <input type="text" name="card_number">
            </div>
            <div class="input-group">
                <label>Expiry Date:</label>
                <input type="text" name="expiry_date" placeholder="MM/YY">
            </div>
            <div class="input-group">
                <label>CVV:</label>
                <input type="password" name="cvv">
            </div>
        </div>

        <!-- UPI Payment Fields -->
        <div id="upi-fields" class="payment-fields" style="display:none;">
            <div class="input-group">
                <label>UPI ID:</label>
                <input type="text" name="upi_id">
            </div>
        </div>

        <!-- Internet Banking Fields -->
        <div id="netbanking-fields" class="payment-fields" style="display:none;">
            <div class="input-group">
                <label>Bank Name:</label>
                <input type="text" name="bank_name">
            </div>
            <div class="input-group">
                <label>User ID:</label>
                <input type="text" name="bank_user">
            </div>
            <div class="input-group">
                <label>Password:</label>
                <input type="password" name="bank_pass">
            </div>
        </div>

        <!-- Debit Card Fields -->
        <div id="debit-fields" class="payment-fields" style="display:none;">
            <div class="input-group">
                <label>Card Number:</label>
                <input type="text" name="debit_card_number">
            </div>
            <div class="input-group">
                <label>Expiry Date:</label>
                <input type="text" name="debit_expiry" placeholder="MM/YY">
            </div>
            <div class="input-group">
                <label>CVV:</label>
                <input type="password" name="debit_cvv">
            </div>
        </div>

        <button type="submit" class="btn">Pay Now</button>
    </form>
</div>

</body>
</html>