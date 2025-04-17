<?php
$conn = new mysqli("localhost", "root", "", "smartstock");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_po'])) {
    $po_id = $_POST['po_id'];
    $customer_id = $_POST['po_customer'];
    $order_id = $_POST['order_id'];
    $delivery_date = $_POST['delivery_date'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("INSERT INTO PurchaseOrders (POID, CustomerID, OrderID, DeliveryDate, Quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $po_id, $customer_id, $order_id, $delivery_date, $quantity);
    $stmt->execute();
    $stmt->close();

    header("Location: purchaseorder.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_po'])) {
    $po_id = $_POST['edit_po_id'];
    $customer_id = $_POST['edit_po_customer'];
    $delivery_date = $_POST['edit_delivery_date'];
    $quantity = $_POST['edit_quantity'];

    $stmt = $conn->prepare("UPDATE PurchaseOrders SET CustomerID=?, DeliveryDate=?, Quantity=? WHERE POID=?");
    $stmt->bind_param("ssis", $customer_id, $delivery_date, $quantity, $po_id);
    $stmt->execute();
    $stmt->close();

    header("Location: purchaseorder.php");
    exit;
}

if (isset($_GET['delete'])) {
    $po_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM PurchaseOrders WHERE POID = ?");
    $stmt->bind_param("s", $po_id);
    $stmt->execute();
    $stmt->close();

    header("Location: purchaseorder.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase Orders | SmartStock ERP</title>
    <link rel="stylesheet" href="style.css" />
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

    <div class="dashboard">
        <h1>Purchase Orders</h1>
        <div class="actions">
            <button class="btn" onclick="showCreateForm()">Create Purchase Order</button>
            <form method="GET">
                <input type="text" id="searchInput" name="search" placeholder="Search PO ID" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                <button class="btn" type="Submit">Search</button>
            </form>
        </div>

        <!-- This is the PO create form-->
        <div id="createForm" class="toggle-form">
            <form method="POST">
                <h2>Create New Purchase Order</h2>
                <label for="po_id">PO ID</label>
                <input type="text" id="po_id" name="po_id" required />

                <label for="po_customer">Customer ID</label>
                <input type="text" id="po_customer" name="po_customer" required />

                <label for="order_id">Order ID</label>
                <input type="text" id="order_id" name="order_id" required />

                <label for="delivery_date">Est. Delivery Date</label>
                <input type="date" id="delivery_date" name="delivery_date" required />

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required />

                <button class="btn" type="submit" name="create_po" onclick="alert('PO Created.')">Create</button>
            </form>
        </div>
        <!-- This is the PO edit form-->
        <div id="editForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Purchase Order</h2>
                    <div class="form-group">
                        <label>PO ID</label>
                        <input type="text" id="edit_po_id" name="edit_po_id" readonly />
                    </div>
                    <div class="form-group">
                        <label>Customer ID</label>
                        <input type="text" id="edit_po_customer" name="edit_po_customer" />
                    </div>
                    <div class="form-group">
                        <label>Order ID</label>
                        <input type="text" id="edit_order_id" name="edit_order_id" disabled />
                    </div>
                    <div class="form-group">
                        <label>Estimated Delivery Date</label>
                        <input type="date" id="edit_delivery_date" name="edit_delivery_date" />
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="edit_quantity" name="edit_quantity" />
                    </div>
                    <button class="btn" type="submit" name="update_po" onclick="alert('Changes saved.')">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>PO ID</th>
                    <th>Customer ID</th>
                    <th>Order ID</th>
                    <th>Delivery Date</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <!--Temporary data. Will need to implement PHP to pull from database.-->
            <tbody id="poTable">
                <?php
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if (!empty($search)) {
                    $stmt = $conn->prepare("SELECT * FROM PurchaseOrders WHERE POID LIKE ?");
                    $likeSearch = "%$search%";
                    $stmt->bind_param("s", $likeSearch);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } 
                else
                    $result = $conn->query("SELECT * FROM PurchaseOrders");

                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["POID"]) ?></td>
                        <td><?= htmlspecialchars($row["CustomerID"]) ?></td>
                        <td><?= htmlspecialchars($row["OrderID"]) ?></td>
                        <td><?= htmlspecialchars($row["DeliveryDate"]) ?></td>
                        <td><?= htmlspecialchars($row["Quantity"]) ?></td>
                        <td class="actions-row">
                            <button class="btn"
                                onclick="showEditForm(
                                '<?= htmlspecialchars($row['POID']) ?>',
                                '<?= htmlspecialchars($row['CustomerID']) ?>',
                                '<?= htmlspecialchars($row['OrderID']) ?>',
                                '<?= htmlspecialchars($row['DeliveryDate']) ?>',
                                '<?= htmlspecialchars($row['Quantity']) ?>'
                                )">Edit</button>
                            <a class="btn btn-danger" href="purchaseorder.php?delete=<?= urlencode($row['POID']) ?>"
                                onclick="return confirm('Are you sure you want to delete this Purchase Order?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        function showCreateForm() {
            const form = document.getElementById('createForm');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }

        function showEditForm(po_id, po_customer, order_id, delivery_date, quantity) {
            document.getElementById('edit_po_id').value = po_id;
            document.getElementById('edit_po_customer').value = po_customer;
            document.getElementById('edit_order_id').value = order_id;
            document.getElementById('edit_delivery_date').value = delivery_date;
            document.getElementById('edit_quantity').value = quantity;
            document.getElementById('editForm').style.display = 'flex';
        }

        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>

</html>