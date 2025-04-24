<?php
$conn = new mysqli("localhost", "root", "", "smartstock");

if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function query_count($conn, $table)
{
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM $table");
        $row = $stmt->fetch_assoc();
        return $row['total'];
    } catch (mysqli_sql_exception $e) {
        return "Error: Table '$table' not found.";
    }
}

function query_tables($conn, $table, $orderBy = '', $limit = 0)
{
    try {
        $query = "SELECT * FROM $table";
        if ($orderBy !== '') {
            $query .= " ORDER BY $orderBy DESC";
        }
        if ($limit > 0) {
            $query .= " LIMIT $limit";
        }
        return $conn->query($query);
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock ERP</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="topbar">
        <div class="titletext"><strong>SmartStock ERP</strong></div>
        <nav class="topnav">
            <a href="index.php">Dashboard</a>
            <a href="orderitems.php">Create Orders</a>
            <a href="inventory.php">Inventory</a>
            <a href="purchaseorder.php">Purchase Orders</a>
            <a href="transferorder.php">Transfer Orders</a>
            <a href="management.php">Management</a>
        </nav>
    </header>

    <main class="dashboard">
        <h2>Dashboard Overview</h2>
        <div class="card-container">
            <div class="card">
                <h3>Total Items</h3>
                <p><?= query_count($conn, 'items') ?></p>
            </div>
            <div class="card">
                <h3>Purchase Orders</h3>
                <p><?= query_count($conn, 'purchaseorders') ?></p>
            </div>
            <div class="card">
                <h3>Transfer Orders</h3>
                <p><?= query_count($conn, 'transferorders') ?></p>
            </div>
        </div>
        <section class="section-table">
            <h2>Recent Purchase Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>PO ID</th>
                        <th>Customer ID</th>
                        <th>Order ID</th>
                        <th>Delivery Date</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $result = query_tables($conn, 'purchaseorders', 'DeliveryDate', 5); ?>
                    <?php if ($result): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["POID"]) ?></td>
                                <td><?= htmlspecialchars($row["CustomerID"]) ?></td>
                                <td><?= htmlspecialchars($row["OrderID"]) ?></td>
                                <td><?= htmlspecialchars($row["DeliveryDate"]) ?></td>
                                <td><?= htmlspecialchars($row["Quantity"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="color:red;">Error: Table 'PurchaseOrders' not found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        <section class="section-table">
            <h2>Recent Transfer Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Transfer ID</th>
                        <th>Transfer Date</th>
                        <th>From Location</th>
                        <th>To Location</th>
                        <th>Item ID</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $result = query_tables($conn, 'transferorders', 'TransferDate', 5); ?>
                    <?php if ($result): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["TransferID"]) ?></td>
                                <td><?= htmlspecialchars($row["TransferDate"]) ?></td>
                                <td><?= htmlspecialchars($row["FromLocation"]) ?></td>
                                <td><?= htmlspecialchars($row["ToLocation"]) ?></td>
                                <td><?= htmlspecialchars($row["ItemID"]) ?></td>
                                <td><?= htmlspecialchars($row["Quantity"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="color:red;">Error: Table 'TransferOrders' not found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>