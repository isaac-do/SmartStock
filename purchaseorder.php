<?php
$conn = new mysqli("localhost", "root", "", "smartstock");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

// Check if tables exists
function table_exists($conn, $table_name)
{
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    return $result && $result->num_rows > 0;
}

// Check if the table has the entity
function exists_in_table($conn, $table, $column, $value) {
    $stmt = $conn->prepare("SELECT 1 FROM $table WHERE $column = ? LIMIT 1");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['create_po'])) {
        if (table_exists($conn, 'PurchaseOrders')) {
            try {
                $po_id = $_POST['po_id'];
                $customer_id = $_POST['po_customer'];
                $order_id = $_POST['order_id'];
                $delivery_date = $_POST['delivery_date'];
                $quantity = $_POST['quantity'];

                // Foreign key checks
                if (!exists_in_table($conn, "Customer", "CustomerID", $customer_id)) {
                    header("Location: error.php?code=fk_po_cust_id_create");
                    exit;
                }

                $stmt = $conn->prepare("INSERT INTO PurchaseOrders (POID, CustomerID, OrderID, DeliveryDate, Quantity) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $po_id, $customer_id, $order_id, $delivery_date, $quantity);
                
                if ($stmt->execute())
                    $successMessage = "Purchase Order created successfully!";
                else
                    $errorMessage = "Error: " . $stmt->error;
            
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                header("Location: error.php?code=unknown&msg=" . urlencode($e->getMessage()));
                exit;
            }
        } else {
            $errorMessage = "Error: The 'PurchaseOrders' table does not exist.";
        }
    }

    if (isset($_POST['update_po'])) {
        $po_id = $_POST['edit_po_id'];
        $customer_id = $_POST['edit_po_customer'];
        $delivery_date = $_POST['edit_delivery_date'];
        $quantity = $_POST['edit_quantity'];

        // Foreign key checks
        if (!exists_in_table($conn, "Customer", "CustomerID", $customer_id)) {
            header("Location: error.php?code=fk_po_cust_id_update");
            exit;
        }

        $stmt = $conn->prepare("UPDATE PurchaseOrders SET CustomerID=?, DeliveryDate=?, Quantity=? WHERE POID=?");
        $stmt->bind_param("ssis", $customer_id, $delivery_date, $quantity, $po_id);

        if ($stmt->execute())
            $successMessage = "Purchase Order updated successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;
    
        $stmt->close();
    }

    if (isset($_POST['delete_po'])) {
        $po_id = $_POST['delete_po_id'];

        $stmt = $conn->prepare("DELETE FROM PurchaseOrders WHERE POID = ?");
        $stmt->bind_param("s", $po_id);

        if ($stmt->execute())
            $successMessage = "Purchase Order deleted successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase Orders</title>
    <link rel="stylesheet" href="style.css" />
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

    <div class="dashboard">
        <h1>Purchase Orders</h1>

        <?php if(isset($successMessage)): ?>
            <div class="notification success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if(isset($errorMessage)): ?>
            <div class="notification error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="actions">
            <button class="btn" onclick="showCreateForm()">Create Purchase Order</button>
            <form method="GET">
                <input type="text" id="searchInput" name="po_search" placeholder="Search PO ID" value="<?= isset($_GET['po_search']) ? htmlspecialchars($_GET['po_search']) : '' ?>" />
                <button class="btn" type="submit">Search</button>
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

                <button class="btn" type="submit" name="create_po">Create</button>
            </form>
        </div>
        <!-- This is the PO edit form-->
        <div id="editForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Purchase Order</h2>
                    <div class="form-group">
                        <label>PO ID [readonly]</label>
                        <input type="text" id="edit_po_id" name="edit_po_id" readonly />
                    </div>
                    <div class="form-group">
                        <label>Customer ID</label>
                        <input type="text" id="edit_po_customer" name="edit_po_customer" />
                    </div>
                    <div class="form-group">
                        <label>Order ID</label>
                        <input type="text" id="edit_order_id" name="edit_order_id" />
                    </div>
                    <div class="form-group">
                        <label>Estimated Delivery Date</label>
                        <input type="date" id="edit_delivery_date" name="edit_delivery_date" />
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="edit_quantity" name="edit_quantity" />
                    </div>
                    <button class="btn" type="submit" name="update_po">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <!-- Confirmation Dialog -->
        <div id="confirmDialog" class="confirm-dialog">
            <div class="confirm-content">
                <h3>Confirm Delete</h3>
                <p>Are you sure you want to delete this Purchase Order? This action cannot be undone.</p>
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" id="delete_po_id" name="delete_po_id" value="">
                    <div class="confirm-buttons">
                        <button type="submit" name="delete_po" class="btn">Delete</button>
                        <button type="button" onclick="closeConfirmDialog()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
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
            <tbody>
                <?php if (table_exists($conn, 'PurchaseOrders')): ?>
                    <?php
                    $search = isset($_GET['po_search']) ? $_GET['po_search'] : '';
                    if (!empty($search)) {
                        $stmt = $conn->prepare("SELECT * FROM PurchaseOrders WHERE POID LIKE ?");
                        $likeSearch = "%$search%";
                        $stmt->bind_param("s", $likeSearch);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else
                        $result = $conn->query("SELECT * FROM PurchaseOrders ORDER BY DeliveryDate DESC");

                    if ($result && $result->num_rows > 0):
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
                                    <button class="btn btn-danger" onclick="confirmDelete('<?= htmlspecialchars($row['POID']) ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="color:gray;">No purchase orders found.</td></tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="color:red;">Error: Table 'PurchaseOrders' not found.</td>
                    </tr>
                <?php endif; ?>
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

        function confirmDelete(po_id) {
            document.getElementById('delete_po_id').value = po_id;
            document.getElementById('confirmDialog').style.display = 'flex';
        }
        
        function closeConfirmDialog() {
            document.getElementById('confirmDialog').style.display = 'none';
        }

        // Auto-hide notifications after 5 seconds
        setTimeout(function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(function(notification) {
                notification.style.display = 'none';
            });
        }, 5000);
    </script>
</body>

</html>