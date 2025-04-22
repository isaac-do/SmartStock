<!DOCTYPE html>
<html>

<head>
    <title>Error</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="error-container">
        <h1 style="color: red;">Error</h1>
        <?php
        $code = $_GET['code'] ?? 'unknown';
        $msg = $_GET['msg'] ?? '';

        switch ($code) {
            case 'fk_customer_salesrep':
                echo "<p>Customer creation failed: The Sales Rep ID you entered does not exist.</p>";
                break;
            case 'fk_supplier_salesrep':
                echo "<p>Supplier creation failed: The Sales Rep ID you entered does not exist.</p>";
                break;
                case 'fk_transfer_order_item_id':
                    echo "<p>Transfer Order creation failed: The Item ID you entered does not exist.</p>";
                    break;
            case 'unknown':
            default:
                echo "<p>An unexpected error occurred.</p>";
                if (!empty($msg)) {
                    echo "<p><strong>Details:</strong> " . htmlspecialchars($msg) . "</p>";
                }
                break;
        }
        ?>
        <nav class="topnav">
            <a href="management.php" class="btn">Back to Management</a>
        </nav>
    </div>
</body>

</html>