<?php
include "AkodePayment.php";

// Configuration
// ! IMPORTANT: Replace these with your actual credentials
$apiUser = "YOUR_API_USER";
$clientId = "YOUR_CLIENT_ID";
$apiPass = "YOUR_SECRET_KEY";
$environment = "https://entegrasyon.tosla.com/api/Payment/"; // Live Environment

// Callback URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$callback_url = "$protocol://$_SERVER[HTTP_HOST]/tosla/callback.php";

// Order Details
// In a real application, you should generate a unique Order ID and save it to your database pending payment.
$orderId = "ORDER-" . time();
$amount = 100; // 1.00 TL (Amount is in kurus, so 100 means 1.00 TL)
$instalment = 0; // 0 for single payment

$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);
$three_d_session_id = null;
$error_message = null;

// Handle initial form submission (Prepare 3D Session)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Step 1: Get ThreeDSessionId from Tosla
        $payment = $gateway->threeDPayment($callback_url, $amount, $instalment, $orderId);

        if (isset($payment->ThreeDSessionId)) {
            $three_d_session_id = $payment->ThreeDSessionId;
            // Store card details to render in the auto-submit form
            $cardName = $_POST['cardName'] ?? '';
            $cardNumber = str_replace(' ', '', $_POST['cardNumber'] ?? '');
            $cardExpiry = $_POST['cardExpiry'] ?? '';
            $cardCvv = $_POST['cardCvv'] ?? '';
        }
        else {
            $error_message = "ThreeDSessionId could not be retrieved. " . json_encode($payment);
        }
    }
    catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$form_post_url = $gateway->getFormUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tosla 3D Payment Integration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; }
        h2 { margin-top: 0; color: #111827; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #374151; font-size: 0.875rem; font-weight: 500; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; box-sizing: border-box; font-size: 1rem; }
        button { width: 100%; background-color: #ef4444; color: white; padding: 0.75rem; border: none; border-radius: 0.375rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s; margin-top: 1rem; }
        button:hover { background-color: #dc2626; }
        .error { color: #dc2626; background-color: #fee2e2; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; }
    </style>
</head>
<body>

<div class="card">
    <?php if ($error_message): ?>
        <div class="error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
        </div>
        <p><a href="index.php">Try Again</a></p>

    <?php
elseif ($three_d_session_id): ?>
        <!-- Step 2: Auto-submit form to Tosla -->
        <h2>Connecting to Bank...</h2>
        <p style="text-align:center;">Please wait while we redirect you to 3D Secure verification.</p>
        
        <form id="threeDForm" role="form" method="post" action="<?php echo $form_post_url; ?>">
            <input type="hidden" name="ThreeDSessionId" value="<?php echo htmlspecialchars($three_d_session_id); ?>">
            <input type="hidden" name="CardHolderName" value="<?php echo htmlspecialchars($cardName); ?>">
            <input type="hidden" name="CardNo" value="<?php echo htmlspecialchars($cardNumber); ?>">
            <input type="hidden" name="ExpireDate" value="<?php echo htmlspecialchars($cardExpiry); ?>">
            <input type="hidden" name="Cvv" value="<?php echo htmlspecialchars($cardCvv); ?>">
            <button type="submit" id="submitBtn" style="display:none;">Click here if not redirected</button>
        </form>
        
        <script>
            // Automatic submit
            document.getElementById('threeDForm').submit();
        </script>

    <?php
else: ?>
        <!-- Step 1: Collect Card Details -->
        <h2>Secure Payment</h2>
        
        <div class="meta" style="text-align:center; margin-bottom:1rem; color:#6b7280;">
            Amount: <strong><?php echo number_format($amount / 100, 2); ?> TL</strong>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Card Holder Name</label>
                <input type="text" name="cardName" placeholder="JOHN DOE" required>
            </div>
            
            <div class="form-group">
                <label>Card Number</label>
                <input type="text" name="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19" required>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Expiry (MM/YY)</label>
                    <input type="text" name="cardExpiry" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>CVV</label>
                    <input type="text" name="cardCvv" placeholder="123" maxlength="4" required>
                </div>
            </div>
            
            <button type="submit">Proceed to Payment</button>
        </form>
    <?php
endif; ?>
</div>

</body>
</html>
