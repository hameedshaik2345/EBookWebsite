<?php
session_start();
require_once 'config.php'; // Fixed path to config.php

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get book ID from URL
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate book ID
if ($book_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch book details
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    header("Location: index.php");
    exit();
}

// Check if user has already purchased this book
$stmt = $conn->prepare("SELECT order_id FROM orders WHERE user_id = ? AND book_id = ? AND status = 'completed'");
$stmt->bind_param("ii", $_SESSION['user_id'], $book_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: download.php?id=" . $book_id);
    exit();
}

// Generate unique order ID
$order_id = 'ORD' . time() . rand(1000, 9999);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = null;
    
    // Sanitize inputs
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
    $pincode = filter_input(INPUT_POST, 'pincode', FILTER_SANITIZE_STRING);

    if (!$payment_method || !$address || !$city || !$state || !$pincode) {
        $error = "All fields are required.";
    } else {
        $payment_details = [];

        switch ($payment_method) {
            case 'upi':
                $upi_id = filter_input(INPUT_POST, 'upi_id', FILTER_SANITIZE_STRING);
                if (!$upi_id) {
                    $error = "UPI ID is required.";
                } else {
                    $payment_details['upi_id'] = $upi_id;
                }
                break;
            case 'card':
                $card_number = filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING);
                $card_holder = filter_input(INPUT_POST, 'card_holder', FILTER_SANITIZE_STRING);
                if (!$card_number || !$card_holder) {
                    $error = "Card details are required.";
                } else {
                    $payment_details['card_number'] = substr($card_number, -4);
                    $payment_details['card_holder'] = $card_holder;
                }
                break;
            case 'netbanking':
                $bank_name = filter_input(INPUT_POST, 'bank_name', FILTER_SANITIZE_STRING);
                if (!$bank_name) {
                    $error = "Bank selection is required.";
                } else {
                    $payment_details['bank_name'] = $bank_name;
                }
                break;
            default:
                $error = "Invalid payment method.";
        }

        if (!$error) {
            // Start transaction
            $conn->begin_transaction();

            try {
                // Insert order into database
                $stmt = $conn->prepare("
                    INSERT INTO orders (
                        order_id, user_id, book_id, amount, payment_method, 
                        payment_details, address, city, state, pincode, 
                        status, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', NOW())
                ");

                if ($stmt) {
                    $payment_details_json = json_encode($payment_details);
                    $stmt->bind_param(
                        "siiissssss",
                        $order_id,
                        $_SESSION['user_id'],
                        $book_id,
                        $book['price'],
                        $payment_method,
                        $payment_details_json,
                        $address,
                        $city,
                        $state,
                        $pincode
                    );

                    if ($stmt->execute()) {
                        $conn->commit();
                        header("Location: download.php?id=" . $book_id);
                        exit();
                    } else {
                        throw new Exception("Error executing statement: " . $stmt->error);
                    }
                } else {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Payment processing failed: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Payment - <?php echo htmlspecialchars($book['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .payment-form {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .method-details {
            display: none;
        }
        .method-details.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-form">
            <h2 class="mb-4">Payment Details</h2>
            
            <div class="book-details mb-4">
                <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                <p class="text-muted">Price: ₹<?php echo number_format($book['price'], 2); ?></p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" id="paymentForm">
                <div class="mb-4">
                    <h5>Select Payment Method</h5>
                    <div class="form-check">
                        <input type="radio" name="payment_method" value="upi" id="upi" class="form-check-input" required>
                        <label for="upi" class="form-check-label">UPI</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="payment_method" value="card" id="card" class="form-check-input">
                        <label for="card" class="form-check-label">Credit/Debit Card</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="payment_method" value="netbanking" id="netbanking" class="form-check-input">
                        <label for="netbanking" class="form-check-label">Net Banking</label>
                    </div>
                </div>

                <!-- UPI Details -->
                <div class="method-details" id="upiDetails">
                    <div class="form-group">
                        <label for="upi_id">UPI ID</label>
                        <input type="text" name="upi_id" id="upi_id" class="form-control" placeholder="username@upi">
                    </div>
                </div>

                <!-- Card Details -->
                <div class="method-details" id="cardDetails">
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" name="card_number" id="card_number" class="form-control" placeholder="1234 5678 9012 3456">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expiry">Expiry Date</label>
                                <input type="text" name="expiry" id="expiry" class="form-control" placeholder="MM/YY">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="password" name="cvv" id="cvv" class="form-control" placeholder="123">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="card_holder">Card Holder Name</label>
                        <input type="text" name="card_holder" id="card_holder" class="form-control">
                    </div>
                </div>

                <!-- Net Banking Details -->
                <div class="method-details" id="netbankingDetails">
                    <div class="form-group">
                        <label for="bank_name">Select Bank</label>
                        <select name="bank_name" id="bank_name" class="form-control">
                            <option value="">Select a bank</option>
                            <option value="sbi">State Bank of India</option>
                            <option value="hdfc">HDFC Bank</option>
                            <option value="icici">ICICI Bank</option>
                            <option value="axis">Axis Bank</option>
                        </select>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="mt-4">
                    <h5>Billing Address</h5>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" name="city" id="city" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" name="state" id="state" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pincode">Pincode</label>
                        <input type="text" name="pincode" id="pincode" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-4">Process Payment</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Show/hide payment method details
            $('input[name="payment_method"]').change(function() {
                $('.method-details').removeClass('active');
                $('#' + $(this).val() + 'Details').addClass('active');
            });

            // Basic form validation
            $('#paymentForm').submit(function(e) {
                const paymentMethod = $('input[name="payment_method"]:checked').val();
                
                if (!paymentMethod) {
                    alert('Please select a payment method');
                    e.preventDefault();
                    return;
                }

                // Validate required fields based on payment method
                switch(paymentMethod) {
                    case 'upi':
                        if (!$('#upi_id').val()) {
                            alert('Please enter UPI ID');
                            e.preventDefault();
                        }
                        break;
                    case 'card':
                        if (!$('#card_number').val() || !$('#expiry').val() || !$('#cvv').val() || !$('#card_holder').val()) {
                            alert('Please fill all card details');
                            e.preventDefault();
                        }
                        break;
                    case 'netbanking':
                        if (!$('#bank_name').val()) {
                            alert('Please select a bank');
                            e.preventDefault();
                        }
                        break;
                }
            });
        });
    </script>
</body>
</html>