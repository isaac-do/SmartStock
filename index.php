<?php
$conn = new mysqli("localhost", "root", "", "smartstock");

if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);
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
                <p>
                <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM items");
                    $row = $result->fetch_assoc();
                    echo $row['total'];?>
                </p>
            </div>
            <div class="card">
                <h3>Purchase Orders</h3>
                <p>
                <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM purchaseorders");
                    $row = $result->fetch_assoc();
                    echo $row['total'];?>
                </p>
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
                    <?php
                    $sql = "SELECT * FROM PurchaseOrders";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["POID"]) ?></td>
                            <td><?= htmlspecialchars($row["CustomerID"]) ?></td>
                            <td><?= htmlspecialchars($row["OrderID"]) ?></td>
                            <td><?= htmlspecialchars($row["DeliveryDate"]) ?></td>
                            <td><?= htmlspecialchars($row["Quantity"]) ?></td>
                        </tr>
                    <?php endwhile; ?>
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
                    <tr>
                        <td>T001</td>
                        <td>01-01-2025</td>
                        <td>Warehouse A</td>
                        <td>Store B</td>
                        <td>ITEM002</td>
                        <td>10</td>
                    </tr>
                    <tr>
                        <td>T002</td>
                        <td>03-10-2023</td>
                        <td>Warehouse B</td>
                        <td>Store A</td>
                        <td>ITEM001</td>
                        <td>20</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>