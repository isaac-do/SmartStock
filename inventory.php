<?php
$conn = new mysqli("localhost", "root", "", "smartstock");

if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

// Check if table exists, if not create it and import CSV data
/*
$checkTable = $conn->query("SHOW TABLES LIKE 'inventory'");
if ($checkTable->num_rows == 0) {
    $createTable = "CREATE TABLE inventory (
        item_id VARCHAR(20) PRIMARY KEY,
        item_name VARCHAR(100) NOT NULL,
        item_type VARCHAR(50) NOT NULL,
        location_id VARCHAR(20) NOT NULL,
        purchase_price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL,
        supplier_id VARCHAR(20) NOT NULL,
        sku VARCHAR(50) NOT NULL,
        upc VARCHAR(50) NOT NULL
    )";
    
    if ($conn->query($createTable) === TRUE) {
        $csvFile = 'items.csv';
        if (file_exists($csvFile)) {
            $file = fopen($csvFile, 'r');
            fgetcsv($file);
            
            while (($line = fgetcsv($file)) !== FALSE) {
                $sql = "INSERT INTO inventory 
                        (item_id, item_name, item_type, location_id, purchase_price, quantity, supplier_id, sku, upc) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssdisss", $line[0], $line[1], $line[2], $line[3], $line[4], $line[5], $line[6], $line[7], $line[8]);
                $stmt->execute();
            }
            fclose($file);
        }
    }
}
*/

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create new item
    if (isset($_POST['create'])) {
        $item_id = $_POST['item_id'];
        $item_name = $_POST['item_name'];
        $item_type = $_POST['item_type'];
        $location_id = $_POST['item_location_id'];
        $purchase_price = $_POST['purchase_price'];
        $quantity = $_POST['quantity'];
        $supplier_id = $_POST['supplier_id'];
        $sku = $_POST['item_sku'];
        $upc = $_POST['item_upc'];
        
        $sql = "INSERT INTO inventory 
                (item_id, item_name, item_type, location_id, purchase_price, quantity, supplier_id, sku, upc) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdisss", $item_id, $item_name, $item_type, $location_id, $purchase_price, $quantity, $supplier_id, $sku, $upc);
        
        if ($stmt->execute()) {
            $successMessage = "Item created successfully!";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }
    }
    
    // Update existing item
    if (isset($_POST['update'])) {
        $item_id = $_POST['edit_item_id'];
        $item_name = $_POST['edit_item_name'];
        $item_type = $_POST['edit_item_type'];
        $location_id = $_POST['edit_item_location_id'];
        $purchase_price = $_POST['edit_purchase_price'];
        $quantity = $_POST['edit_quantity'];
        $supplier_id = $_POST['edit_supplier_id'];
        $sku = $_POST['edit_sku'];
        $upc = $_POST['edit_upc'];
        
        // Remove currency symbol if present
        $purchase_price = str_replace('$', '', $purchase_price);
        
        $sql = "UPDATE inventory SET 
                item_name = ?, 
                item_type = ?, 
                location_id = ?, 
                purchase_price = ?, 
                quantity = ?, 
                supplier_id = ?, 
                sku = ?, 
                upc = ? 
                WHERE item_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssissss", $item_name, $item_type, $location_id, $purchase_price, $quantity, $supplier_id, $sku, $upc, $item_id);
        
        if ($stmt->execute()) {
            $successMessage = "Item updated successfully!";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }
    }
    
    // Delete item
    if (isset($_POST['delete'])) {
        $item_id = $_POST['delete_item_id'];
        
        $sql = "DELETE FROM inventory WHERE item_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $item_id);
        
        if ($stmt->execute()) {
            $successMessage = "Item deleted successfully!";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }
    }
    
    // Handle search functionality
    if (isset($_POST['search'])) {
        $search_term = $_POST['search_term'];
        $sql = "SELECT * FROM inventory WHERE item_id LIKE ? OR item_name LIKE ?";
        $search_param = "%".$search_term."%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Fetch all inventory items
        $result = $conn->query("SELECT * FROM inventory ORDER BY item_id");
    }
} else {
    // Fetch all inventory items on page load
    $result = $conn->query("SELECT * FROM inventory ORDER BY item_id");
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
    <div class="dashboard">
        <h1>Product Management</h1>
        
        <?php if(isset($successMessage)): ?>
            <div class="notification success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if(isset($errorMessage)): ?>
            <div class="notification error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <div class="actions">
            <button class="btn" onclick="showCreateForm()">Create Inventory Item</button>
            <form method="POST" action="" style="display: inline-block;">
                <input type="text" id="searchInput" name="search_term" placeholder="Search Item ID or Name" />
                <button type="submit" name="search" class="btn">Search</button>
            </form>
        </div>

        <!-- This is the inventory create form-->
        <div id="createForm" class="toggle-form" style="display: none;">
            <h2>Create New Inventory Item</h2>
            <form method="POST" action="">
                <label for="item_id">Item ID</label>
                <input type="text" id="item_id" name="item_id" required />

                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" required />

                <label for="item_type">Item Type</label>
                <input type="text" id="item_type" name="item_type" required />

                <label for="item_location_id">Location ID</label>
                <input type="text" id="item_location_id" name="item_location_id" required />

                <label for="purchase_price">Purchase Price</label>
                <input type="number" id="purchase_price" name="purchase_price" step="0.01" required />

                <label for="supplier_id">Supplier ID</label>
                <input type="text" id="supplier_id" name="supplier_id" required />

                <label for="item_sku">SKU</label>
                <input type="text" id="item_sku" name="item_sku" required />

                <label for="item_upc">UPC</label>
                <input type="text" id="item_upc" name="item_upc" required />

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required />

                <button type="submit" name="create" class="btn">Create</button>
                <button type="button" class="btn btn-secondary" onclick="hideCreateForm()">Cancel</button>
            </form>
        </div>
        
        <!-- This is the inventory edit form-->
        <div id="editForm" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <h2>Edit Inventory Item</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Item ID</label>
                        <input type="text" id="edit_item_id" name="edit_item_id" readonly />
                    </div>
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" id="edit_item_name" name="edit_item_name" required />
                    </div>
                    <div class="form-group">
                        <label>Item Type</label>
                        <input type="text" id="edit_item_type" name="edit_item_type" required />
                    </div>
                    <div class="form-group">
                        <label>Location ID</label>
                        <input type="text" id="edit_item_location_id" name="edit_item_location_id" required />
                    </div>
                    <div class="form-group">
                        <label>Purchase Price</label>
                        <input type="text" id="edit_purchase_price" name="edit_purchase_price" required />
                    </div>
                    <div class="form-group">
                        <label>Supplier ID</label>
                        <input type="text" id="edit_supplier_id" name="edit_supplier_id" required />
                    </div>
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" id="edit_sku" name="edit_sku" required />
                    </div>
                    <div class="form-group">
                        <label>UPC</label>
                        <input type="text" id="edit_upc" name="edit_upc" required />
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="edit_quantity" name="edit_quantity" required />
                    </div>
                    <button type="submit" name="update" class="btn">Save Changes</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditForm()">Cancel</button>
                </form>
            </div>
        </div>
        
        <!-- Confirmation Dialog -->
        <div id="confirmDialog" class="confirm-dialog">
            <div class="confirm-content">
                <h3>Confirm Delete</h3>
                <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" id="delete_item_id" name="delete_item_id" value="">
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
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Item Type</th>
                    <th>Location ID</th>
                    <th>Purchase Price</th>
                    <th>On-Hand Quantity</th>
                    <th>Supplier ID</th>
                    <th>SKU</th>
                    <th>UPC</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["item_id"] . "</td>";
                        echo "<td>" . $row["item_name"] . "</td>";
                        echo "<td>" . $row["item_type"] . "</td>";
                        echo "<td>" . $row["location_id"] . "</td>";
                        echo "<td>$" . number_format($row["purchase_price"], 2) . "</td>";
                        echo "<td>" . $row["quantity"] . "</td>";
                        echo "<td>" . $row["supplier_id"] . "</td>";
                        echo "<td>" . $row["sku"] . "</td>";
                        echo "<td>" . $row["upc"] . "</td>";
                        echo "<td class='actions-row'>";
                        echo "<button class='btn' onclick='showEditForm(\"" . $row["item_id"] . "\", \"" . $row["item_name"] . "\", \"" . $row["item_type"] . "\", \"" . $row["location_id"] . "\", \"" . number_format($row["purchase_price"], 2) . "\", \"" . $row["quantity"] . "\", \"" . $row["supplier_id"] . "\", \"" . $row["sku"] . "\", \"" . $row["upc"] . "\")'>Edit</button>";
                        echo "<button class='btn' onclick='confirmDelete(\"" . $row["item_id"] . "\")'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No items found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function showCreateForm() {
            document.getElementById('createForm').style.display = 'block';
            document.getElementById('editForm').style.display = 'none';
        }
        
        function hideCreateForm() {
            document.getElementById('createForm').style.display = 'none';
        }

        function showEditForm(item_id, item_name, item_type, item_location_id, purchase_price, quantity, supplier_id, sku, upc) {
            document.getElementById('edit_item_id').value = item_id;
            document.getElementById('edit_item_name').value = item_name;
            document.getElementById('edit_item_type').value = item_type;
            document.getElementById('edit_item_location_id').value = item_location_id;
            document.getElementById('edit_purchase_price').value = purchase_price;
            document.getElementById('edit_quantity').value = quantity;
            document.getElementById('edit_supplier_id').value = supplier_id;
            document.getElementById('edit_sku').value = sku;
            document.getElementById('edit_upc').value = upc;
            document.getElementById('editForm').style.display = 'flex';
        }

        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
        
        function confirmDelete(item_id) {
            document.getElementById('delete_item_id').value = item_id;
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