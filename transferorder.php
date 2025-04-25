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
    if (isset($_POST['create_to'])) {
        if (table_exists($conn, 'TransferOrders')) {
            try {
                $to_id = $_POST['to_id'];
                $transfer_date = $_POST['transfer_date'];
                $from_location = $_POST['from_location'];
                $to_location = $_POST['to_location'];
                $item_id = $_POST['item_id'];
                $quantity = $_POST['quantity'];

                // Foreign key checks
                if (!exists_in_table($conn, "Locations", "LocationID", $from_location)) {
                    header("Location: error.php?code=fk_transfer_location_creation");
                    exit;
                }
                if (!exists_in_table($conn, "Locations", "LocationID", $to_location)) {
                    header("Location: error.php?code=fk_transfer_location_creation");
                    exit;
                }
                if (!exists_in_table($conn, "Items", "ItemID", $item_id)) {
                    header("Location: error.php?code=fk_transfer_order_item_id_creation");
                    exit;
                }
                
                $stmt = $conn->prepare("INSERT INTO TransferOrders (TransferID, TransferDate, FromLocation, ToLocation, ItemID, Quantity) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssi", $to_id, $transfer_date, $from_location, $to_location, $item_id, $quantity);

                if ($stmt->execute())
                    $successMessage = "Transfer Order created successfully!";
                else
                    $errorMessage = "Error: " . $stmt->error;

                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                header("Location: error.php?code=unknown&msg=" . urlencode($e->getMessage()));
                exit;
            }
        } else {
            $errorMessage = "Error: The 'TransferOrders' table does not exist.";
        }
    } 

    if (isset($_POST['update_to'])) {
        try {
            $to_id = $_POST['edit_to_id'];
            $transfer_date = $_POST['edit_transfer_date'];
            $from_location = $_POST['edit_from_location'];
            $to_location = $_POST['edit_to_location'];
            $item_id = $_POST['edit_item_id'];
            $quantity = $_POST['edit_quantity'];

            // Foreign key checks
            if (!exists_in_table($conn, "Locations", "LocationID", $from_location)) {
                header("Location: error.php?code=fk_transfer_location_edit");
                exit;
            }
            if (!exists_in_table($conn, "Locations", "LocationID", $to_location)) {
                header("Location: error.php?code=fk_transfer_location_edit");
                exit;
            }
            if (!exists_in_table($conn, "Items", "ItemID", $item_id)) {
                header("Location: error.php?code=fk_transfer_order_item_id_edit");
                exit;
            }
            

            $stmt = $conn->prepare("UPDATE TransferOrders SET TransferID=?, TransferDate=?, FromLocation=?, ToLocation=?, ItemID=?, Quantity=? WHERE TransferID=?");
            $stmt->bind_param("sssssis", $to_id, $transfer_date, $from_location, $to_location, $item_id, $quantity, $to_id);

            if ($stmt->execute())
                $successMessage = "Transfer Order updated successfully!";
            else
                $errorMessage = "Error: " . $stmt->error;

            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            header("Location: error.php?code=unknown&msg=" . urlencode($e->getMessage()));
            exit;
        }
    }

    if (isset($_POST['delete_to'])) {
        $to_id = $_POST['delete_to_id'];

        $stmt = $conn->prepare("DELETE FROM TransferOrders WHERE TransferID = ?");
        $stmt->bind_param("s", $to_id);

        if ($stmt->execute())
            $successMessage = "Transfer Order deleted successfully!";
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
    <title>Transfer Orders</title>
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
        <h1>Transfer Orders</h1>

        <?php if (isset($successMessage)): ?>
            <div class="notification success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="notification error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="actions">
            <button class="btn" onclick="showCreateForm()">Create Transfer Order</button>
            <form method="GET">
                <input type="text" id="searchInput" name="search" placeholder="Search Transfer ID" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                <button class="btn" type="Submit">Search</button>
            </form>
        </div>

        <div id="createForm" class="toggle-form">
            <form method="POST">
                <h2>Create New Transfer Order</h2>
                <label for="to_id">TransferID</label>
                <input type="text" id="to_id" name="to_id" required />

                <label for="transfer_date">Transfer Date</label>
                <input type="date" id="transfer_date" name="transfer_date" required />

                <label for="from_location">From Location</label>
                <input type="text" id="from_location" name="from_location" required />

                <label for="to_location">To Location</label>
                <input type="text" id="to_location" name="to_location" required />

                <label for="item_id">Item ID</label>
                <input type="text" id="item_id" name="item_id" required />

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required />

                <button class="btn" type="submit" name="create_to">Create</button>
            </form>
        </div>
        <!-- This is the TO edit form-->
        <div id="editForm" class="modal-overlay">
            <form method="POST">
                <div class="modal-content">
                    <h2>Edit Purchase Order</h2>
                    <div class="form-group">
                        <label>Transfer ID [readonly]</label>
                        <input type="text" id="edit_to_id" name="edit_to_id" readonly />
                    </div>
                    <div class="form-group">
                        <label>Transfer Date</label>
                        <input type="date" id="edit_transfer_date" name="edit_transfer_date" />
                    </div>
                    <div class="form-group">
                        <label>From Location</label>
                        <input type="text" id="edit_from_location" name="edit_from_location" />
                    </div>
                    <div class="form-group">
                        <label>To Location</label>
                        <input type="text" id="edit_to_location" name="edit_to_location" />
                    </div>
                    <div class="form-group">
                        <label>Item ID</label>
                        <input type="text" id="edit_item_id" name="edit_item_id" />
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="edit_quantity" name="edit_quantity" />
                    </div>
                    <button class="btn" type="submit" name="update_to">Save Changes</button>
                    <button class="btn btn-secondary" onclick="closeEditForm()">Cancel</button>
                </div>
            </form>
        </div>
        <!-- Confirmation Dialog -->
        <div id="confirmDialog" class="confirm-dialog">
            <div class="confirm-content">
                <h3>Confirm Delete</h3>
                <p>Are you sure you want to delete this Transfer Order? This action cannot be undone.</p>
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" id="delete_to_id" name="delete_to_id" value="">
                    <div class="confirm-buttons">
                        <button type="submit" name="delete_to" class="btn">Delete</button>
                        <button type="button" onclick="closeConfirmDialog()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Transfer ID</th>
                    <th>Transfer Date</th>
                    <th>From Location</th>
                    <th>To Location</th>
                    <th>Item ID</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (table_exists($conn, 'transferorders')): ?>
                    <?php
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    if (!empty($search)) {
                        $stmt = $conn->prepare("SELECT * FROM TransferOrders WHERE TransferID LIKE ?");
                        $likeSearch = "%$search%";
                        $stmt->bind_param("s", $likeSearch);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else
                        $result = $conn->query("SELECT * FROM TransferOrders ORDER BY TransferDate DESC");
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["TransferID"]) ?></td>
                                <td><?= htmlspecialchars($row["TransferDate"]) ?></td>
                                <td><?= htmlspecialchars($row["FromLocation"]) ?></td>
                                <td><?= htmlspecialchars($row["ToLocation"]) ?></td>
                                <td><?= htmlspecialchars($row["ItemID"]) ?></td>
                                <td><?= htmlspecialchars($row["Quantity"]) ?></td>
                                <td class="actions-row">
                                    <button class="btn"
                                        onclick="showEditForm(
                                        '<?= htmlspecialchars($row['TransferID']) ?>',
                                        '<?= htmlspecialchars($row['TransferDate']) ?>',
                                        '<?= htmlspecialchars($row['FromLocation']) ?>',
                                        '<?= htmlspecialchars($row['ToLocation']) ?>',
                                        '<?= htmlspecialchars($row['ItemID']) ?>',
                                        '<?= htmlspecialchars($row['Quantity']) ?>'
                                        )">Edit</button>
                                    <button class="btn btn-danger" onclick="confirmDelete('<?= htmlspecialchars($row['TransferID']) ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="color:gray;">No transfer orders found.</td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="color:red;">Error: Table 'transferorders' not found.</td>
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

        function showEditForm(to_id, transfer_date, from_location, to_location, item_id, quantity) {
            document.getElementById('edit_to_id').value = to_id;
            document.getElementById('edit_transfer_date').value = transfer_date;
            document.getElementById('edit_from_location').value = from_location;
            document.getElementById('edit_to_location').value = to_location;
            document.getElementById('edit_item_id').value = item_id;
            document.getElementById('edit_quantity').value = quantity;
            document.getElementById('editForm').style.display = 'flex';
        }

        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }

        function confirmDelete(to_id) {
            document.getElementById('delete_to_id').value = to_id;
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