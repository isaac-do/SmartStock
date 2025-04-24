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
            case 'fk_customer_salesrep_creation':
                echo "<p>Customer Record creation failed: The Sales Rep ID you entered does not exist.</p>";
                $link = "management.php";
                break;
            case 'fk_supplier_salesrep_creation':
                echo "<p>Supplier Reocrd creation failed: The Sales Rep ID you entered does not exist.</p>";
                $link = "management.php";
                break;
            case 'fk_customer_salesrep_edit':
                echo "<p>Customer Record edit failed: The Sales Rep ID you entered does not exist.</p>";
                $link = "management.php";
                break;
            case 'fk_supplier_salesrep_edit':
                echo "<p>Supplier Reocrd edit failed: The Sales Rep ID you entered does not exist.</p>";
                $link = "management.php";
                break;
            case 'fk_transfer_order_item_id_creation':
                echo "<p>Transfer Order creation failed: The Item ID you entered does not exist.</p>";
                $link = "transferorder.php";
                break;
            case 'fk_transfer_location_creation':
                echo "<p>Transfer Order creation failed: The Location ID you entered does not exist.</p>";
                $link = "transferorder.php";
                break;
            case 'fk_transfer_order_item_id_edit':
                echo "<p>Transfer Order edit failed: The Item ID you entered does not exist.</p>";
                $link = "transferorder.php";
                break;
            case 'fk_transfer_location_edit':
                echo "<p>Transfer Order edit failed: The Location ID you entered does not exist.</p>";
                $link = "transferorder.php";
                break;
            case 'fk_order_items_id_creation':
                echo "<p>Order creation failed: The Item ID you entered does not exist.</p>";
                $link = "orderitems.php";
                break;
            case 'fk_order_items_id_edit':
                echo "<p>Order edit failed: The Item ID you entered does not exist.</p>";
                $link = "orderitems.php";
                break;
            case 'fk_items_id_delete':
                echo "<p>Item delete failed: There are orders with this item!</p>";
                $link = "inventory.php";
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
            <a href="<?= $link ?>" class="btn">Go Back</a>
        </nav>
    </div>
</body>

</html>