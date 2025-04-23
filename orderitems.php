<?php
$conn = new mysqli("localhost", "root", "", "smartstock");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

// Check if tables exists
function table_exists($conn, $table_name)
{
    $table_name_escaped = $conn->real_escape_string($table_name);
    $result = $conn->query("SHOW TABLES LIKE '$table_name_escaped'");
    return $result && $result->num_rows > 0;
}

// Check if the table has the entity
function exists_in_table($conn, $table, $column, $value)
{
    $stmt = $conn->prepare("SELECT 1 FROM $table WHERE $column = ? LIMIT 1");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['create_order'])) {
        try {
            $order_id = $_POST['order_id'];
            $item_id = $_POST['item_id'];
            $unit_price = $_POST['unit_price'];
            $quantity_ordered = $_POST['quantity_ordered'];

            // Foreign key checks
            if (!exists_in_table($conn, "Items", "ItemID", $item_id)) {
                header("Location: error.php?code=fk_order_items_id");
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO OrderItems (OrderID, ItemID, UnitPrice, QuantityOrdered) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssdi", $order_id, $item_id, $unit_price, $quantity_ordered);

            if ($stmt->execute())
                $successMessage = "Order created successfully!";
            else
                $errorMessage = "Error: " . $stmt->error;

            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            header("Location: error.php?code=unknown&msg=" . urlencode($e->getMessage()));
            exit;
        }
    }

    if (isset($_POST['update_order'])) {
        $order_id = $_POST['edit_order_id'];
        $item_id = $_POST['edit_item_id'];
        $unit_price = $_POST['edit_unit_price'];
        $quantity_ordered = $_POST['edit_quantity_ordered'];

        $stmt = $conn->prepare("UPDATE OrderItems SET ItemID=?, UnitPrice=?, QuantityOrdered=? WHERE OrderID=?");
        $stmt->bind_param("sdis", $item_id, $unit_price, $quantity_ordered, $order_id);

        if ($stmt->execute())
            $successMessage = "Order updated successfully!";
        else
            $errorMessage = "Error: " . $stmt->error;

        $stmt->close();
    }

    if (isset($_POST['delete'])) {
        $order_id = $_POST['delete_order_id'];

        $stmt = $conn->prepare("DELETE FROM OrderItems WHERE OrderID = ?, ItemID = ?");
        $stmt->bind_param("s", $order_id);

        if ($stmt->execute())
            $successMessage = "Order deleted successfully!";
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
    <title>Orders Items</title>
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
        <h1>Orders</h1>

        <?php if (isset($successMessage)): ?>
            <div class="notification success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="notification error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="actions">
            <button class="btn" onclick="showCreateForm()">Create Order</button>
            <form method="GET">
                <input type="text" id="searchInput" name="search" placeholder="Search Order ID" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                <button class="btn" type="Submit">Search</button>
            </form>
        </div>

        <div id="createForm" class="toggle-form">
            <form method="POST">
                <h2>Create New Order</h2>
                <label for="order_id">Order ID</label>
                <input type="text" id="order_id" name="order_id" required />

                <label for="item_id">Item ID</label>
                <input type="text" id="item_id" name="item_id" required />

                <label for="unit_price">Unit Price</label>
                <input type="number" id="unit_price" name="unit_price" step="0.01" required />

                <label for="quantity_ordered">Quantity</label>
                <input type="number" id="quantity_ordered" name="quantity_ordered" required />

                <button class="btn" type="submit" name="create_order">Create</button>
            </form>
        </div>
        <div id="editForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Order</h2>
                    <div class="form-group">
                        <label>Order ID [readonly]</label>
                        <input type="text" id="edit_order_id" name="edit_order_id" readonly />
                    </div>
                    <div class="form-group">
                        <label>Item ID</label>
                        <input type="text" id="edit_item_id" name="edit_item_id" />
                    </div>
                    <div class="form-group">
                        <label>Unit Price</label>
                        <input type="number" id="edit_unit_price" name="edit_unit_price" step="0.01" />
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="edit_quantity_ordered" name="edit_quantity_ordered" />
                    </div>
                    <button class="btn" type="submit" name="update_order">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <!-- Confirmation Dialog -->
        <div id="confirmDialog" class="confirm-dialog">
            <div class="confirm-content">
                <h3>Confirm Delete</h3>
                <p>Are you sure you want to delete this Order? This action cannot be undone.</p>
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" id="delete_order_id" name="delete_order_id" value="">
                    <div class="confirm-buttons">
                        <button type="submit" name="delete" class="btn">Delete</button>
                        <button type="button" onclick="closeConfirmDialog()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Item ID</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (table_exists($conn, 'orderitems')): ?>
                    <?php
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    if (!empty($search)) {
                        $stmt = $conn->prepare("SELECT * FROM OrderItems WHERE OrderID LIKE ?");
                        $likeSearch = "%$search%";
                        $stmt->bind_param("s", $likeSearch);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else
                        $result = $conn->query("SELECT * FROM OrderItems");
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["OrderID"]) ?></td>
                                <td><?= htmlspecialchars($row["ItemID"]) ?></td>
                                <td>$<?= number_format($row["UnitPrice"], 2) ?></td>
                                <td><?= number_format($row["QuantityOrdered"]) ?></td>
                                <td class="actions-row">
                                    <button class="btn"
                                        onclick="showEditForm(
                                        '<?= htmlspecialchars($row['OrderID']) ?>',
                                        '<?= htmlspecialchars($row['ItemID']) ?>',
                                        '<?= number_format($row['UnitPrice'], 2) ?>',
                                        '<?= number_format($row['QuantityOrdered']) ?>'
                                        )">Edit</button>
                                    <button class="btn btn-danger" onclick="confirmDelete('<?= htmlspecialchars($row['OrderID']) ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="color:gray;">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="color:red;">Error: Table 'orderitems' not found.</td>
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

        function showEditForm(order_id, item_id, unit_price, quantity_ordered) {
            document.getElementById('edit_order_id').value = order_id;
            document.getElementById('edit_item_id').value = item_id;
            document.getElementById('edit_unit_price').value = unit_price;
            document.getElementById('edit_quantity_ordered').value = quantity_ordered;

            document.getElementById('editForm').style.display = 'flex';
        }

        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }

        function confirmDelete(order_id) {
            document.getElementById('delete_order_id').value = order_id;
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