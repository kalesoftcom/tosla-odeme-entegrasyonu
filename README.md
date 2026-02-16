# Tosla (AkÖde) Payment Gateway Integration for PHP

A simple, ready-to-use PHP integration for Tosla (AkÖde) Virtual POS. 

This repository contains a clean implementation of the 3D Secure payment flow using the AkÖde PHP SDK structure.

## Features

-   **3D Secure Payment**: Full implementation of the 3D Secure flow (Initialize -> Redirect -> Callback).
-   **Secure**: Uses `AkodePayment` class for hash generation and validation.
-   **Easy Config**: Single file configuration.
-   **Connection Fixes**: Includes cURL optimizations (`CURLOPT_CONNECTTIMEOUT`, `IPv4 Resolve`) for better compatibility with various hosting environments.

## File Structure

-   `AkodePayment.php`: The core library file (SDK).
-   `index.php`: The payment page. Collects card info and initiates the transaction.
-   `callback.php`: The return URL handler. Verifies the transaction and captures the payment (`PostAuth`).

## Installation

1.  Clone or download this repository.
2.  Upload the files to your web server.
3.  Open `index.php` and `callback.php`.

## Configuration

Edit **both** `index.php` and `callback.php` and update the following variables with your API credentials:

```php
$apiUser = "YOUR_API_USER";
$clientId = "YOUR_CLIENT_ID";
$apiPass = "YOUR_API_PASS";
$environment = "https://entegrasyon.tosla.com/api/Payment/"; // Use Production URL for Live
```

## Usage

1.  Navigate to `http://yourdomain.com/path/to/index.php`.
2.  Enter the amount and order ID details (or integrate with your cart system).
3.  The user enters card details and clicks "Pay".
4.  The system handles the 3D Secure redirect and returns to `callback.php` with the result.

## Requirements

-   PHP 7.0 or higher
-   cURL extension enabled
-   OpenSSL extension enabled

## Disclaimer

This is a sample implementation. Please ensure you comply with PCI-DSS requirements when handling credit card data.
