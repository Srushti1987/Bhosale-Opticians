<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('Location: index_updated.php');
    exit();
}

$order_id = intval($_GET['order_id']);
$conn = getDBConnection();

// Fetch order details - Allow admin to view any order
if ($_SESSION['user_role'] == 'admin') {
    $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = $order_id";
} else {
    $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = $order_id AND o.user_id = {$_SESSION['user_id']}";
}

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: index_updated.php');
    exit();
}

$order = $result->fetch_assoc();

// Fetch order items
$items_sql = "SELECT oi.*, p.name as product_name, p.category, p.gender 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_sql);
$items = [];
while($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

$conn->close();

// Calculate totals
$subtotal = $order['total_amount'];
$tax = $subtotal * 0.18;
$total = $subtotal + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $order_id ?> - Bhosale Opticians</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; background: white; }
            .invoice-box {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
            }
            .container { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
            .badge { border: 1px solid #999; color: #333 !important; background: #eee !important; }
            a { text-decoration: none !important; color: #333 !important; }
            .alert { border: 1px solid #ccc !important; background: #f9f9f9 !important; color: #333 !important; }
            .table { font-size: 13px; }
            .text-brown { color: #8B4513 !important; }
            .watermark {
                display: block !important;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-30deg);
                font-size: 80px;
                color: rgba(0,0,0,0.04);
                font-weight: bold;
                z-index: 9999;
                pointer-events: none;
            }
        }
        @media screen {
            .watermark { display: none; }
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
            background: white;
        }
        .invoice-header-bar {
            background: linear-gradient(135deg, #8B4513, #D2691E);
            color: white;
            padding: 15px 20px;
            border-radius: 6px 6px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .btn-pdf {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-pdf:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(220,53,69,0.4); }
    </style>
</head>
<body>

    <div class="container my-5">
        <div class="text-center mb-4 no-print">
            <h2>Order Placed Successfully! 🎉</h2>
            <p class="text-muted">Your order has been confirmed. Invoice details below.</p>
            <?php if (isset($_GET['feedback']) && $_GET['feedback'] == 'success'): ?>
            <div class="alert alert-success">
                <strong>✅ Thank you for your feedback!</strong> Your review has been submitted successfully.
            </div>
            <?php endif; ?>
        </div>

        <div class="invoice-box bg-white">
            <!-- Watermark (print only) -->
            <div class="watermark">BHOSALE OPTICIANS</div>

            <!-- Colored Header Bar -->
            <div class="invoice-header-bar d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0 fw-bold">Bhosale Opticians</h2>
                    <small>Premium Eyewear Store</small>
                </div>
                <div class="text-end">
                    <h3 class="mb-0 fw-bold">INVOICE</h3>
                    <small>#<?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?></small>
                </div>
            </div>

            <!-- Header -->
            <div class="row mb-4">
                <div class="col-6">
                    <p class="mb-0">1st Floor, Silver Springs, Hotgi Road</p>
                    <p class="mb-0">Solapur, Maharashtra 413003</p>
                    <p class="mb-0">📞 9960815363</p>
                    <p class="mb-0">✉️ bhosaleopticians@gmail.com</p>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-0"><strong>Invoice #:</strong> <?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?></p>
                    <p class="mb-0"><strong>Date:</strong> <?= date('d M, Y', strtotime($order['created_at'])) ?></p>
                    <p class="mb-0"><strong>Time:</strong> <?= date('h:i A', strtotime($order['created_at'])) ?></p>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning"><?= ucfirst($order['status']) ?></span></p>
                </div>
            </div>

            <hr>

            <!-- Customer Details -->
            <div class="row mb-4">
                <div class="col-6">
                    <h5>Bill To:</h5>
                    <p class="mb-0"><strong><?= htmlspecialchars($order['user_name']) ?></strong></p>
                    <p class="mb-0"><?= htmlspecialchars($order['user_email']) ?></p>
                </div>
                <div class="col-6">
                    <h5>Ship To:</h5>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>
            </div>

            <!-- Order Items -->
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?> • <?= htmlspecialchars($item['gender']) ?></td>
                        <td class="text-center"><?= $item['quantity'] ?></td>
                        <td class="text-end">₹<?= number_format($item['price'], 2) ?></td>
                        <td class="text-end">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                        <td class="text-end">₹<?= number_format($subtotal, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>GST (18%):</strong></td>
                        <td class="text-end">₹<?= number_format($tax, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                        <td class="text-end text-success"><strong>FREE</strong></td>
                    </tr>
                    <tr class="table-active">
                        <td colspan="4" class="text-end"><h5 class="mb-0">Grand Total:</h5></td>
                        <td class="text-end"><h5 class="mb-0 text-brown">₹<?= number_format($total, 2) ?></h5></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Payment Info -->
            <?php if (!empty($order['payment_id'])): ?>
            <div class="alert alert-success">
                <strong>✓ Payment Status:</strong> PAID<br>
                <strong>Payment Method:</strong> Online Payment (Razorpay)<br>
                <strong>Payment ID:</strong> <?= htmlspecialchars($order['payment_id']) ?><br>
                <strong>Transaction Date:</strong> <?= date('d M, Y h:i A', strtotime($order['created_at'])) ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <strong>Payment Method:</strong> Cash on Delivery (COD)<br>
                <strong>Note:</strong> Please keep ₹<?= number_format($total, 2) ?> ready for payment upon delivery.
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['payment']) && $_GET['payment'] == 'success'): ?>
            <div class="alert alert-success">
                <strong>🎉 Payment Successful!</strong> Your payment has been received and verified. Order is confirmed.
            </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="text-center mt-4">
                <p class="text-muted mb-0">Thank you for shopping with Bhosale Opticians!</p>
                <p class="text-muted">For any queries, contact us at bhosaleopticians@gmail.com</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4 no-print">
            <button onclick="downloadPDF()" class="btn-pdf me-2">
                📥 Download PDF
            </button>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-lg me-2">
                🖨️ Print Invoice
            </button>
            <a href="user/orders.php" class="btn btn-outline-primary btn-lg me-2">
                📦 My Orders
            </a>
            <a href="index_updated.php" class="btn btn-outline-secondary btn-lg">
                🛍️ Continue Shopping
            </a>
        </div>

        <!-- Clear Cart -->
        <script>
            localStorage.removeItem('cart');

            function downloadPDF() {
                // Change page title to use as filename
                const originalTitle = document.title;
                document.title = 'Invoice-<?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?>-BhosaleOpticians';

                // Show print dialog (user selects "Save as PDF")
                window.print();

                // Restore title after
                setTimeout(() => { document.title = originalTitle; }, 1000);
            }

            <?php if(isset($_GET['download'])): ?>
            // Auto-trigger PDF download
            window.addEventListener('load', function() {
                setTimeout(downloadPDF, 500);
            });
            <?php endif; ?>
        </script>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
