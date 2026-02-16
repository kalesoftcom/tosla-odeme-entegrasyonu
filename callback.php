<?php
include "AkodePayment.php";

// Configuration (Must match index.php)
// ! IMPORTANT: Replace these with your actual credentials
$apiUser = "YOUR_API_USER";
$clientId = "YOUR_CLIENT_ID";
$apiPass = "YOUR_SECRET_KEY";
$environment = "https://entegrasyon.tosla.com/api/Payment/";

$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);

// Set the POST data received from the callback
$gateway->setPost($_POST);

$isSuccess = false;
$error = null;
$orderId = $gateway->getOrderId();
$finalResult = null;

// Verify Hash and determine success
if ($gateway->isSuccessfull()) {
    // 3D Auth successful, now finalize the payment (PostAuth)
    try {
        // We need the amount. In a real app, retrieve from DB using OrderID. 
        // For testing, we use the sample amount.
        $amount = 100;

        $finalResult = $gateway->threeDPostAuth($orderId, $amount);

        // Check both ResponseCode (standard) and Code (alternative) for success
        $successCode = false;
        if (isset($finalResult->ResponseCode) && $finalResult->ResponseCode === "00") {
            $successCode = true;
        }
        elseif (isset($finalResult->Code) && ($finalResult->Code === 0 || $finalResult->Code === "00")) {
            $successCode = true;
        }

        if ($successCode) {
            $isSuccess = true;
            $error = "Payment Completed. Transaction ID: " . ($finalResult->TransactionId ?? 'N/A');
        }
        else {
            $isSuccess = false;
            $error = "PostAuth Failed: " . ($finalResult->ResponseMessage ?? $finalResult->Message ?? json_encode($finalResult));
        }
    }
    catch (Exception $e) {
        $isSuccess = false;
        $error = "System Error during PostAuth: " . $e->getMessage();
    }
}
else {
    $isSuccess = false;
    $error = $gateway->getError() ?? "3D Verification Failed or Hash Mismatch";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center; }
        .icon { font-size: 3rem; margin-bottom: 1rem; }
        .success { color: #10b981; }
        .fail { color: #ef4444; }
        h2 { margin: 0 0 1rem 0; color: #111827; }
        p { color: #6b7280; margin-bottom: 1.5rem; word-break: break-all; }
        .btn { display: inline-block; background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 0.375rem; text-decoration: none; font-weight: 500; transition: background-color 0.2s; }
        .btn:hover { background-color: #2563eb; }
        .details { background: #f9fafb; padding: 1rem; border-radius: 0.5rem; text-align: left; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .details div { margin-bottom: 0.5rem; }
    </style>
</head>
<body>

<div class="card">
    <?php if ($isSuccess): ?>
        <div class="icon success">✓</div>
        <h2>Payment Successful</h2>
        <div class="details">
            <div><strong>Order ID:</strong> <?php echo htmlspecialchars($orderId); ?></div>
            <div><strong>Result:</strong> <?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php
else: ?>
        <div class="icon fail">✕</div>
        <h2>Payment Failed</h2>
        <div class="details">
            <div><strong>Order ID:</strong> <?php echo htmlspecialchars($orderId); ?></div>
            <div><strong>Reason:</strong> <?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php
endif; ?>
    
    <a href="index.php" class="btn">New Payment</a>
</div>

</body>
</html>
