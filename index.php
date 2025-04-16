<?php
$conn = new mysqli("localhost", "root", "", "mystock"); // DB name = smartstock

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
            <a href="index.html">Dashboard</a>
            <a href="inventory.html">Inventory</a>
            <a href="purchaseorder.html">Purchase Orders</a>
            <a href="transferorder.html">Transfer Orders</a>
            <a href="management.html">Management</a>
        </nav>
    </header>

    <main class="dashboard">
        <h2>Dashboard Overview</h2>
        <div class="card-container">
            <div class="card">
                <h3>Total Items</h3>
                <p>1,234</p>
            </div>
            <div class="card">
                <h3>Open Orders</h3>
                <p>28</p>
            </div>
            <div class="card">
                <h3>Shipped</h3>
                <p>10</p>
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
                        <th>Est. Delivery Date</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM PurchaseOrders ORDER BY DeliveryDate DESC LIMIT 5";
                    $result = $conn->query($sql);

                    while ($row = $result->fetch_assoc()):
                    ?>
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